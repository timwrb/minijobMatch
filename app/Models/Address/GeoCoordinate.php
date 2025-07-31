<?php

declare(strict_types=1);

namespace App\Models\Address;

use Database\Factories\Address\GeoCoordinateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property float $latitude
 * @property float $longitude
 */
class GeoCoordinate extends Model
{
    /** @use HasFactory<GeoCoordinateFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }
}
