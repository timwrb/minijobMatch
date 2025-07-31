<?php

declare(strict_types=1);

use App\Models\Address\City;
use App\Models\Address\GeoCoordinate;
use App\Models\Address\State;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // The RefreshDatabase trait will handle migrations automatically
    // States are populated via the migration, so they'll be available
});

it('can seed city data from geojson fixture', function () {
    // Get the fixture file path
    $fixturePath = base_path('tests/fixtures/gemeinden_simplify200.geojson');

    // Verify fixture exists
    expect(file_exists($fixturePath))->toBeTrue();

    // Verify Schleswig-Holstein state exists (RS code "01")
    $schleswigHolstein = State::where('rs_code', '01')->first();
    expect($schleswigHolstein)->not()->toBeNull()
        ->and($schleswigHolstein->name)->toBe('Schleswig-Holstein');

    // Execute the command with the fixture file
    $this->artisan('app:seed-city-data', ['file' => $fixturePath])
        ->assertExitCode(0);

    // Verify that exactly 3 cities were created
    expect(City::count())->toBe(3)
        ->and(GeoCoordinate::count())->toBe(3);

    // Verify specific cities from the fixture
    $flensburg = City::where('name', 'Flensburg')->first();
    expect($flensburg)->not()->toBeNull()
        ->and($flensburg->zip)->toBe('24937')
        ->and($flensburg->state_id)->toBe($schleswigHolstein->id)
        ->and($flensburg->geoCoordinate)->not()->toBeNull()
        ->and($flensburg->geoCoordinate->latitude)->toBe(54.78252)
        ->and($flensburg->geoCoordinate->longitude)->toBe(9.43751);

    $kiel = City::where('name', 'Kiel')->first();
    expect($kiel)->not()->toBeNull()
        ->and($kiel->zip)->toBe('24103')
        ->and($kiel->state_id)->toBe($schleswigHolstein->id)
        ->and($kiel->geoCoordinate)->not()->toBeNull()
        ->and($kiel->geoCoordinate->latitude)->toBe(54.321775)
        ->and($kiel->geoCoordinate->longitude)->toBe(10.13727);

    $luebeck = City::where('name', 'Lübeck')->first();
    expect($luebeck)->not()->toBeNull()
        ->and($luebeck->zip)->toBe('23552')
        ->and($luebeck->state_id)->toBe($schleswigHolstein->id)
        ->and($luebeck->geoCoordinate)->not()->toBeNull()
        ->and($luebeck->geoCoordinate->latitude)->toBe(53.866269)
        ->and($luebeck->geoCoordinate->longitude)->toBe(10.683932);
});

it('handles rs code mapping correctly', function () {
    $fixturePath = base_path('tests/fixtures/gemeinden_simplify200.geojson');

    $this->artisan('app:seed-city-data', ['file' => $fixturePath])
        ->assertExitCode(0);

    // All cities in the fixture have RS codes starting with "01" (Schleswig-Holstein)
    $schleswigHolstein = State::where('rs_code', '01')->first();
    $cities = City::all();

    expect($cities->count())->toBe(3);

    foreach ($cities as $city) {
        expect($city->state_id)->toBe($schleswigHolstein->id);
    }
});

it('parses german decimal coordinates correctly', function () {
    $fixturePath = base_path('tests/fixtures/gemeinden_simplify200.geojson');

    $this->artisan('app:seed-city-data', ['file' => $fixturePath])
        ->assertExitCode(0);

    // Verify that German decimal format (comma) was converted to float (dot)
    $coordinates = GeoCoordinate::all();

    expect($coordinates->count())->toBe(3);

    foreach ($coordinates as $coordinate) {
        expect($coordinate->latitude)->toBeFloat()
            ->and($coordinate->longitude)->toBeFloat()
            ->and($coordinate->latitude)->toBeGreaterThan(0)
            ->and($coordinate->longitude)->toBeGreaterThan(0);
    }
});

it('prevents duplicate cities', function () {
    $fixturePath = base_path('tests/fixtures/gemeinden_simplify200.geojson');

    // Run the command twice
    $this->artisan('app:seed-city-data', ['file' => $fixturePath])
        ->assertExitCode(0);
    $this->artisan('app:seed-city-data', ['file' => $fixturePath])
        ->assertExitCode(0);

    // Should still only have 3 cities (no duplicates)
    expect(City::count())->toBe(3)
        ->and(GeoCoordinate::count())->toBe(3);
});

it('fails gracefully with invalid file', function () {
    $this->artisan('app:seed-city-data', ['file' => 'nonexistent.json'])
        ->expectsOutputToContain('GeoJSON file not found at:')
        ->assertExitCode(0);

    // Verify no cities were created
    expect(City::count())->toBe(0);
});

it('fails gracefully with invalid json', function () {
    // Create a temporary invalid JSON file
    $tempFile = storage_path('temp_invalid.json');
    file_put_contents($tempFile, '{"invalid": json}');

    $this->artisan('app:seed-city-data', ['file' => $tempFile])
        ->expectsOutputToContain('Failed to parse GeoJSON data')
        ->assertExitCode(0);

    // Verify no cities were created
    expect(City::count())->toBe(0);

    // Clean up
    unlink($tempFile);
});

it('validates required geojson structure', function () {
    // Create a valid JSON but invalid GeoJSON structure
    $tempFile = storage_path('temp_invalid_geojson.json');
    file_put_contents($tempFile, json_encode(['type' => 'InvalidType']));

    $this->artisan('app:seed-city-data', ['file' => $tempFile])
        ->expectsOutputToContain('Invalid GeoJSON structure')
        ->assertExitCode(0);

    // Verify no cities were created
    expect(City::count())->toBe(0);

    // Clean up
    unlink($tempFile);
});

it('handles missing data gracefully and shows warnings', function () {
    // Create a GeoJSON with incomplete data to test warnings
    $incompleteData = [
        'type' => 'FeatureCollection',
        'features' => [
            [
                'type' => 'Feature',
                'properties' => [
                    'GEN' => 'TestCity',
                    'RS' => '99999999',  // Invalid RS code
                ],
                'geometry' => ['type' => 'Point', 'coordinates' => [0, 0]],
            ],
        ],
    ];

    $tempFile = storage_path('temp_incomplete.geojson');
    file_put_contents($tempFile, json_encode($incompleteData));

    $this->artisan('app:seed-city-data', ['file' => $tempFile])
        ->assertExitCode(0);

    // Should have created no cities due to invalid RS code
    expect(City::count())->toBe(0);

    // Clean up
    unlink($tempFile);
});

it('processes data and creates proper relationships', function () {
    $fixturePath = base_path('tests/fixtures/gemeinden_simplify200.geojson');

    $this->artisan('app:seed-city-data', ['file' => $fixturePath])
        ->assertExitCode(0);

    // Verify relationships are properly created
    $cities = City::with('geoCoordinate', 'state')->get();

    expect($cities->count())->toBe(3);

    foreach ($cities as $city) {
        expect($city->geoCoordinate)->not()->toBeNull()
            ->and($city->state)->not()->toBeNull()
            ->and($city->zip)->not()->toBeEmpty()
            ->and($city->name)->not()->toBeEmpty();
    }
});
