<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Address\City;
use App\Models\Address\GeoCoordinate;
use App\Models\Address\State;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @see https://opendatalab.de/projects/geojson-utilities/
 *
 * This command is used to seed city data into the database.
 * Download the latest GeoJSON data for cities in Germany.
 * Then, put the .geojson file into the storage/app/util/gemeinden_simplify200.geojson. This directory is gitignored.
 */
class SeedCityDataCommand extends Command
{
    protected $signature = 'app:seed-city-data {file? : Path to the GeoJSON file}';

    protected $description = 'Import German city data from GeoJSON file';

    private const DEFAULT_FILE_PATH = 'storage/app/util/gemeinden_simplify200.geojson';
    private const CHUNK_SIZE = 500;

    /** @var array<string, int> */
    private array $statesByRsCode = [];

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        $filePath = $this->argument('file') ?? self::DEFAULT_FILE_PATH;

        if (!$this->validateFile($filePath)) {
            return Command::SUCCESS;
        }

        $features = $this->parseGeoJsonFile($filePath);
        if ($features === null) {
            return Command::SUCCESS;
        }

        $this->cacheStates();
        $cityData = $this->transformFeaturesToCityData($features);
        $this->persistCityData($cityData);

        return Command::SUCCESS;
    }

    private function validateFile(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            $this->error("GeoJSON file not found at: {$filePath}");
            return false;
        }

        return true;
    }

    /** @return array<mixed>|null */
    private function parseGeoJsonFile(string $filePath): ?array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            $this->error('Failed to read GeoJSON file');
            return null;
        }

        $json = json_decode($content);
        if (!$json) {
            $this->error('Failed to parse GeoJSON data');
            return null;
        }

        if (!is_object($json) || !isset($json->features) || !is_array($json->features)) {
            $this->error('Invalid GeoJSON structure. Expected FeatureCollection.');
            return null;
        }

        return $json->features;
    }

    private function cacheStates(): void
    {
        /** @var array<string, int> $states */
        $states = State::all()->pluck('id', 'rs_code')->toArray();
        $this->statesByRsCode = $states;
    }

    /**
     * @param array<mixed> $features
     * @return Collection<int, array{name: string, zip: string, state_id: int, latitude: float, longitude: float}>
     */
    private function transformFeaturesToCityData(array $features): Collection
    {
        return collect($features)
            ->map(fn(mixed $feature) => $this->transformFeatureToCity($feature))
            ->filter()
            ->values();
    }

    /** @return array{name: string, zip: string, state_id: int, latitude: float, longitude: float}|null */
    private function transformFeatureToCity(mixed $feature): ?array
    {
        if (!is_object($feature) || !isset($feature->properties) || !is_object($feature->properties)) {
            return null;
        }

        $properties = $feature->properties;
        $cityName = $this->getCityName($properties);
        $stateId = $this->getStateId($properties, $cityName);
        $zip = $this->getZipCode($properties, $cityName);
        $coordinates = $this->getCoordinates($properties, $cityName);

        if (!$stateId || !$zip || !$coordinates) {
            return null;
        }

        return [
            'name' => $cityName,
            'zip' => $zip,
            'state_id' => $stateId,
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
        ];
    }


    private function getCityName(object $properties): string
    {
        return isset($properties->GEN) && is_string($properties->GEN)
            ? $properties->GEN
            : 'Unknown';
    }

    private function getStateId(object $properties, string $cityName): ?int
    {
        $rs = $this->extractRsCode($properties);
        $bundeslandCode = substr($rs, 0, 2);
        $stateId = $this->statesByRsCode[$bundeslandCode] ?? null;

        if (!$stateId) {
            $this->warn("No state found for RS code {$bundeslandCode}: {$cityName}");
        }

        return $stateId;
    }

    private function extractRsCode(object $properties): string
    {
        if (isset($properties->RS) && is_string($properties->RS)) {
            return $properties->RS;
        }

        if (isset($properties->AGS) && is_string($properties->AGS)) {
            return $properties->AGS;
        }

        return '';
    }

    private function getZipCode(object $properties, string $cityName): ?string
    {
        $zip = $this->extractZipFromDestatis($properties)
            ?? $this->extractZipFromPLZ($properties);

        if (!$zip) {
            $this->warn("No ZIP code found: {$cityName}");
        }

        return $zip;
    }

    private function extractZipFromDestatis(object $properties): ?string
    {
        if (!isset($properties->destatis) || !is_object($properties->destatis)) {
            return null;
        }

        $zip = $properties->destatis->zip ?? null;
        return $this->normalizeToString($zip);
    }

    private function extractZipFromPLZ(object $properties): ?string
    {
        $zip = $properties->PLZ ?? null;
        return $this->normalizeToString($zip);
    }

    /** @return array{latitude: float, longitude: float}|null */
    private function getCoordinates(object $properties, string $cityName): ?array
    {
        if (!isset($properties->destatis) || !is_object($properties->destatis)) {
            $this->warn("No coordinates found: {$cityName}");
            return null;
        }

        $destatis = $properties->destatis;
        if (!isset($destatis->center_lon, $destatis->center_lat)) {
            $this->warn("No coordinates found: {$cityName}");
            return null;
        }

        return [
            'longitude' => $this->parseGermanCoordinate($destatis->center_lon),
            'latitude' => $this->parseGermanCoordinate($destatis->center_lat),
        ];
    }

    private function parseGermanCoordinate(mixed $coordinate): float
    {
        $value = $this->normalizeToString($coordinate) ?? '0';
        return (float) str_replace(',', '.', $value);
    }

    private function normalizeToString(mixed $value): ?string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        return null;
    }

    /** @param Collection<int, array{name: string, zip: string, state_id: int, latitude: float, longitude: float}> $cityData
     * @throws Throwable
     */
    private function persistCityData(Collection $cityData): void
    {
        if ($cityData->isEmpty()) {
            $this->error('No valid cities to import');
            return;
        }

        $bar = $this->output->createProgressBar($cityData->count());
        $bar->start();

        $cityData->chunk(self::CHUNK_SIZE)->each(function (Collection $chunk) use ($bar): void {
            DB::transaction(function () use ($chunk, $bar): void {
                /** @var array{name: string, zip: string, state_id: int, latitude: float, longitude: float} $cityInfo */
                foreach ($chunk as $cityInfo) {
                    $this->createCityIfNotExists($cityInfo);
                    $bar->advance();
                }
            });
        });

        $bar->finish();
        $this->newLine();
    }

    /** @param array{name: string, zip: string, state_id: int, latitude: float, longitude: float} $cityInfo */
    private function createCityIfNotExists(array $cityInfo): void
    {
        if ($this->cityExists($cityInfo)) {
            return;
        }

        $geoCoordinate = $this->findOrCreateGeoCoordinate(
            $cityInfo['latitude'],
            $cityInfo['longitude']
        );

        City::query()->create([
            'name' => $cityInfo['name'],
            'zip' => $cityInfo['zip'],
            'state_id' => $cityInfo['state_id'],
            'geo_coordinate_id' => $geoCoordinate->id,
        ]);
    }

    /** @param array{name: string, zip: string, state_id: int, latitude: float, longitude: float} $cityInfo */
    private function cityExists(array $cityInfo): bool
    {
        return City::query()->where([
            ['name', $cityInfo['name']],
            ['zip', $cityInfo['zip']],
            ['state_id', $cityInfo['state_id']],
        ])->exists();
    }

    private function findOrCreateGeoCoordinate(float $latitude, float $longitude): GeoCoordinate
    {
        return GeoCoordinate::query()->firstOrCreate([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }
}
