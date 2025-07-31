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
        ->expectsOutput('Starting German city data import...')
        ->expectsOutput('Processing 3 cities...')
        ->expectsOutput('City import completed successfully!')
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

    $this->artisan('app:seed-city-data', ['file' => $fixturePath]);

    // All cities in the fixture have RS codes starting with "01" (Schleswig-Holstein)
    $schleswigHolstein = State::where('rs_code', '01')->first();
    $cities = City::all();

    foreach ($cities as $city) {
        expect($city->state_id)->toBe($schleswigHolstein->id);
    }
});

it('parses german decimal coordinates correctly', function () {
    $fixturePath = base_path('tests/fixtures/gemeinden_simplify200.geojson');

    $this->artisan('app:seed-city-data', ['file' => $fixturePath]);

    // Verify that German decimal format (comma) was converted to float (dot)
    $coordinates = GeoCoordinate::all();

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
    $this->artisan('app:seed-city-data', ['file' => $fixturePath]);
    $this->artisan('app:seed-city-data', ['file' => $fixturePath]);

    // Should still only have 3 cities (no duplicates)
    expect(City::count())->toBe(3)
        ->and(GeoCoordinate::count())->toBe(3);
});

it('fails gracefully with invalid file', function () {
    $this->artisan('app:seed-city-data', ['file' => 'nonexistent.json'])
        ->expectsOutput('GeoJSON file not found at: nonexistent.json')
        ->assertExitCode(0);
});

it('fails gracefully with invalid json', function () {
    // Create a temporary invalid JSON file
    $tempFile = storage_path('temp_invalid.json');
    file_put_contents($tempFile, '{"invalid": json}');

    $this->artisan('app:seed-city-data', ['file' => $tempFile])
        ->expectsOutput('Failed to parse GeoJSON data')
        ->assertExitCode(0);

    // Clean up
    unlink($tempFile);
});

it('validates required geojson structure', function () {
    // Create a valid JSON but invalid GeoJSON structure
    $tempFile = storage_path('temp_invalid_geojson.json');
    file_put_contents($tempFile, json_encode(['type' => 'InvalidType']));

    $this->artisan('app:seed-city-data', ['file' => $tempFile])
        ->expectsOutput('Invalid GeoJSON structure. Expected FeatureCollection.')
        ->assertExitCode(0);

    // Clean up
    unlink($tempFile);
});
