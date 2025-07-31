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
        if ($raw === false) {
            $this->error('Failed to read GeoJSON file');
            return;
        }
        
        $json = json_decode($raw);

        if (! $json) {
            $this->error('Failed to parse GeoJSON data');

            return;
        }

        if (! is_object($json) || ! isset($json->features) || ! is_array($json->features)) {
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
            ->map(function (mixed $feature) use ($statesByRsCode): ?array {
                if (! is_object($feature) || ! isset($feature->properties) || ! is_object($feature->properties)) {
                    return null;
                }
                // Extract RS code from geodata (first 2 digits identify Bundesland)
                $properties = $feature->properties;
                $rs = (isset($properties->RS) && is_string($properties->RS)) ? $properties->RS : 
                      ((isset($properties->AGS) && is_string($properties->AGS)) ? $properties->AGS : '');
                $bundeslandRsCode = substr($rs, 0, 2);

                // Find the corresponding state
                $stateId = $statesByRsCode[$bundeslandRsCode] ?? null;

                $cityName = (isset($properties->GEN) && is_string($properties->GEN)) ? $properties->GEN : 'Unknown';
                
                if (! $stateId) {
                    $this->warn("Skipping city - no Bundesland found for RS code: $bundeslandRsCode (City: {$cityName})");

                    return null;
                }

                // Try to get ZIP code from available properties
                $zip = null;
                if (isset($properties->destatis) && is_object($properties->destatis) && isset($properties->destatis->zip)) {
                    $zip = is_string($properties->destatis->zip) ? $properties->destatis->zip : 
                           (is_numeric($properties->destatis->zip) ? (string)$properties->destatis->zip : null);
                } elseif (isset($properties->PLZ)) {
                    $zip = is_string($properties->PLZ) ? $properties->PLZ : 
                           (is_numeric($properties->PLZ) ? (string)$properties->PLZ : null);
                }
                
                if (! $zip) {
                    $this->warn("Skipping city - no ZIP code found: {$cityName}");

                    return null;
                }

                // Parse coordinates from destatis if available, otherwise skip
                if (! isset($properties->destatis) || ! is_object($properties->destatis)) {
                    $this->warn("Skipping city - no destatis data found: {$cityName}");

                    return null;
                }
                
                $destatis = $properties->destatis;
                if (! isset($destatis->center_lon) || ! isset($destatis->center_lat)) {
                    $this->warn("Skipping city - no coordinates found: {$cityName}");
                    return null;
                }
                
                $centerLon = is_string($destatis->center_lon) ? $destatis->center_lon : 
                             (is_numeric($destatis->center_lon) ? (string)$destatis->center_lon : '0');
                $centerLat = is_string($destatis->center_lat) ? $destatis->center_lat : 
                             (is_numeric($destatis->center_lat) ? (string)$destatis->center_lat : '0');
                
                $longitude = (float) str_replace(',', '.', $centerLon);
                $latitude = (float) str_replace(',', '.', $centerLat);

                return [
                    'name' => $cityName,
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
