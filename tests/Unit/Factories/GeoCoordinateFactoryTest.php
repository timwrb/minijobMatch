<?php

declare(strict_types=1);

use App\Models\Address\GeoCoordinate;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('geo coordinate factory creates valid geo coordinate', function () {
    $coordinate = GeoCoordinate::factory()->create();

    expect($coordinate)->toBeInstanceOf(GeoCoordinate::class)
        ->and($coordinate->latitude)->toBeFloat()
        ->and($coordinate->longitude)->toBeFloat()
        ->and($coordinate->latitude)->toBeBetween(-90, 90)
        ->and($coordinate->longitude)->toBeBetween(-180, 180);
});

test('geo coordinate factory germany state works', function () {
    $coordinate = GeoCoordinate::factory()->germany()->create();

    expect($coordinate)->toBeInstanceOf(GeoCoordinate::class)
        ->and($coordinate->latitude)->toBeBetween(47.2, 55.1)
        ->and($coordinate->longitude)->toBeBetween(5.9, 15.0);
});

test('geo coordinate factory can create multiple coordinates', function () {
    $coordinates = GeoCoordinate::factory()->count(3)->create();

    expect($coordinates)->toHaveCount(3)
        ->and($coordinates->first())->toBeInstanceOf(GeoCoordinate::class);
});
