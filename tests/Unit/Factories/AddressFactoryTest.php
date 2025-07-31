<?php

declare(strict_types=1);

use App\Models\Address\Address;
use App\Models\Address\City;
use App\Models\Address\GeoCoordinate;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('address factory creates valid address', function () {
    $address = Address::factory()->create();

    expect($address)->toBeInstanceOf(Address::class)
        ->and($address->country_iso_code)->not->toBeNull()
        ->and($address->city_id)->toBeInt()
        ->and($address->city)->toBeInstanceOf(City::class)
        ->and($address->country_iso_code)->toHaveLength(2);
});

test('address factory with german data works', function () {
    $address = Address::factory()->withGermanData()->create();

    expect($address)->toBeInstanceOf(Address::class)
        ->and($address->country_iso_code)->toBe('DE')
        ->and($address->street->city->state->country_iso_code)->toBe('DE')
        ->and($address->geoCoordinate)->toBeInstanceOf(GeoCoordinate::class);
});

test('address factory without geo coordinate works', function () {
    $address = Address::factory()->withoutGeoCoordinate()->create();

    expect($address)->toBeInstanceOf(Address::class)
        ->and($address->geo_coordinate_id)->toBeNull()
        ->and($address->geoCoordinate)->toBeNull();
});

test('address factory can create multiple addresses', function () {
    $addresses = Address::factory()->count(3)->create();

    expect($addresses)->toHaveCount(3)
        ->and($addresses->first())->toBeInstanceOf(Address::class);
});

test('address city only factory works for vacancy providers', function () {
    $address = Address::factory()->cityOnly()->create();

    expect($address)->toBeInstanceOf(Address::class)
        ->and($address->house_number)->toBeNull()
        ->and($address->address_addition)->toBeNull()
        ->and($address->street_id)->toBeNull()
        ->and($address->street)->toBeNull()
        ->and($address->city)->toBeInstanceOf(City::class)
        ->and($address->city->state->country_iso_code)->toBe('DE');
});

test('address full address attribute works with complete data', function () {
    $address = Address::factory()->withGermanData()->create([
        'house_number' => '123',
        'address_addition' => 'Apt 4B',
    ]);

    $fullAddress = $address->full_address;

    expect($fullAddress)->toContain($address->street->name)
        ->and($fullAddress)->toContain('123')
        ->and($fullAddress)->toContain('Apt 4B')
        ->and($fullAddress)->toContain($address->city->name)
        ->and($fullAddress)->toContain($address->city->zip)
        ->and($fullAddress)->toContain('DE');
});

test('address full address attribute works with city only', function () {
    $address = Address::factory()->cityOnly()->create();

    $fullAddress = $address->full_address;

    expect($fullAddress)->toContain($address->city->name)
        ->and($fullAddress)->toContain($address->city->zip)
        ->and($fullAddress)->toContain($address->city->state->name)
        ->and($fullAddress)->toContain('DE')
        ->and($fullAddress)->not->toContain('null');
});
