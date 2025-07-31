<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Address\City;
use App\Models\Address\GeoCoordinate;
use App\Models\Address\State;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedCityDataCommand extends Command
{
    /**
     * @see https://opendatalab.de/projects/geojson-utilities/
     *
     * @note This command is used to seed city data into the database.
     * Download the latest GeoJSON data for cities in Germany.
     * Then, put the .geojson file into the storage/app/util/gemeinden_simplify200.geojson. This directory is gitignored.
     */

    /** @var string */
    protected $signature = 'app:seed-city-data {file? : Path to the GeoJSON file}';

    /** @var string */
    protected $description = 'Import German city data from GeoJSON file with automatic Bundesland assignment';

    protected string $defaultFilePath = 'storage/app/util/gemeinden_simplify200.geojson';

    public function handle(): void
    {
        $this->info('Starting German city data import...');

        $filePath = $this->argument('file') ?? $this->defaultFilePath;

        if (! file_exists($filePath)) {
            $this->error("GeoJSON file not found at: {$filePath}");

            return;
        }

        $raw = file_get_contents($filePath);
        $json = json_decode($raw);

        if (! $json) {
            $this->error('Failed to parse GeoJSON data');

            return;
        }

        if (! isset($json->features)) {
            $this->error('Invalid GeoJSON structure. Expected FeatureCollection.');

            return;
        }

        $data = $json->features;

        $this->info('Processing '.count($data).' cities...');

        // Cache states by rs_code for fast lookup
        $statesByRsCode = State::pluck('id', 'rs_code')->toArray();

        // Process and transform data
        /** @var \Illuminate\Support\Collection<int, array{name: string, zip: string, state_id: int, latitude: float, longitude: float}> $cityData */
        $cityData = collect($data)
            ->map(function (object $feature) use ($statesByRsCode): ?array {
                // Extract RS code from geodata (first 2 digits identify Bundesland)
                $rs = $feature->properties->RS ?? $feature->properties->AGS ?? '';
                $bundeslandRsCode = substr($rs, 0, 2);

                // Find the corresponding state
                $stateId = $statesByRsCode[$bundeslandRsCode] ?? null;

                if (! $stateId) {
                    $this->warn("Skipping city - no Bundesland found for RS code: $bundeslandRsCode (City: {$feature->properties->GEN})");

                    return null;
                }

                // Try to get ZIP code from available properties
                $zip = $feature->properties->destatis->zip ?? $feature->properties->PLZ ?? null;
                if (! $zip) {
                    $this->warn("Skipping city - no ZIP code found: {$feature->properties->GEN}");

                    return null;
                }

                // Parse coordinates from destatis if available, otherwise skip
                if (! isset($feature->properties->destatis)) {
                    $this->warn("Skipping city - no destatis data found: {$feature->properties->GEN}");

                    return null;
                }

                $longitude = (float) str_replace(',', '.', $feature->properties->destatis->center_lon);
                $latitude = (float) str_replace(',', '.', $feature->properties->destatis->center_lat);

                return [
                    'name' => $feature->properties->GEN,
                    'zip' => $zip,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'state_id' => $stateId,
                ];
            })
            ->filter() // Remove null entries
            ->values();

        $this->info("Valid cities to process: {$cityData->count()}");

        // Process in chunks for memory efficiency and performance
        $bar = $this->output->createProgressBar($cityData->count());
        $bar->start();

        $cityData->chunk(500)->each(function (\Illuminate\Support\Collection $chunk) use ($bar) {
            DB::transaction(function () use ($chunk, $bar) {
                /** @var array{name: string, zip: string, state_id: int, latitude: float, longitude: float} $cityInfo */
                foreach ($chunk as $cityInfo) {
                    // Check if city already exists
                    $existingCity = City::where('name', $cityInfo['name'])
                        ->where('zip', $cityInfo['zip'])
                        ->where('state_id', $cityInfo['state_id'])
                        ->first();

                    if ($existingCity) {
                        $bar->advance();

                        continue;
                    }

                    // Check if GeoCoordinate already exists
                    $geoCoordinate = GeoCoordinate::where('latitude', $cityInfo['latitude'])
                        ->where('longitude', $cityInfo['longitude'])
                        ->first();

                    if (! $geoCoordinate) {
                        $geoCoordinate = GeoCoordinate::create([
                            'latitude' => $cityInfo['latitude'],
                            'longitude' => $cityInfo['longitude'],
                        ]);
                    }

                    // Create city
                    City::create([
                        'name' => $cityInfo['name'],
                        'zip' => $cityInfo['zip'],
                        'state_id' => $cityInfo['state_id'],
                        'geo_coordinate_id' => $geoCoordinate->id,
                    ]);

                    $bar->advance();
                }
            });
        });

        $bar->finish();
        $this->newLine();
        $this->info('City import completed successfully!');

        // Show summary
        $totalCities = City::count();
        $totalCoordinates = GeoCoordinate::count();
        $this->info("Total cities in database: $totalCities");
        $this->info("Total coordinates in database: $totalCoordinates");
    }
}
