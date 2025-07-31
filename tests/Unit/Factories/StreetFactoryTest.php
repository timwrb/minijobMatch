<?php

declare(strict_types=1);

use App\Models\Address\City;
use App\Models\Address\Street;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('street factory creates valid street', function () {
    $street = Street::factory()->create();

    expect($street)->toBeInstanceOf(Street::class)
        ->and($street->name)->not->toBeNull()
        ->and($street->city_id)->toBeInt()
        ->and($street->city)->toBeInstanceOf(City::class);
});

test('street factory german streets works', function () {
    $street = Street::factory()->germanStreets()->create();

    expect($street)->toBeInstanceOf(Street::class)
        ->and($street->city->state->country_iso_code)->toBe('DE')
        ->and($street->name)->toBeIn([
            'Hauptstraße', 'Bahnhofstraße', 'Kirchstraße', 'Poststraße', 'Marktplatz',
            'Schulstraße', 'Gartenstraße', 'Dorfstraße', 'Lindenstraße', 'Berliner Straße',
        ]);
});

test('street factory can create multiple streets', function () {
    $streets = Street::factory()->count(3)->create();

    expect($streets)->toHaveCount(3)
        ->and($streets->first())->toBeInstanceOf(Street::class);
});
