<?php

declare(strict_types=1);

use App\Models\Address\City;
use App\Models\Address\GeoCoordinate;
use App\Models\Address\State;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('city factory creates valid city', function () {
    $city = City::factory()->create();

    expect($city)->toBeInstanceOf(City::class)
        ->and($city->name)->not->toBeNull()
        ->and($city->zip)->not->toBeNull()
        ->and($city->state_id)->toBeInt()
        ->and($city->geo_coordinate_id)->toBeInt()
        ->and($city->state)->toBeInstanceOf(State::class)
        ->and($city->geoCoordinate)->toBeInstanceOf(GeoCoordinate::class);
});

test('city factory with german state works', function () {
    $city = City::factory()->withGermanState()->create();

    expect($city)->toBeInstanceOf(City::class)
        ->and($city->state->country_iso_code)->toBe('DE')
        ->and($city->zip)->toMatch('/^\d{5}$/');
});

test('city factory can create multiple cities', function () {
    $cities = City::factory()->count(3)->create();

    expect($cities)->toHaveCount(3)
        ->and($cities->first())->toBeInstanceOf(City::class);
});
