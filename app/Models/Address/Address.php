<?php

declare(strict_types=1);

namespace App\Models\Address;

use Carbon\Carbon;
use Database\Factories\Address\AddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $country_iso_code
 * @property string|null $house_number
 * @property string|null $address_addition
 * @property int|null $street_id
 * @property int $city_id
 * @property int|null $geo_coordinate_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Address extends Model
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Address\Street, $this>
     */
    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Address\City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Address\GeoCoordinate, $this>
     */
    public function geoCoordinate(): BelongsTo
    {
        return $this->belongsTo(GeoCoordinate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, never>
     */
    protected function fullAddress(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $parts = [];
            // Add street and house number if available
            if ($this->street && $this->house_number) {
                $parts[] = $this->street->name.' '.$this->house_number;
            }
            // Add address addition if available
            if ($this->address_addition) {
                $parts[] = $this->address_addition;
            }
            // Always add city information
            if ($this->city) {
                $parts[] = $this->city->zip.' '.$this->city->name;
                if ($this->city->state) {
                    $parts[] = $this->city->state->name;
                }
            }
            $parts[] = strtoupper($this->country_iso_code);

            return implode(', ', array_filter($parts));
        });
    }
}
