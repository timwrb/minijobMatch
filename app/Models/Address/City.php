<?php

declare(strict_types=1);

namespace App\Models\Address;

use Carbon\Carbon;
use Database\Factories\Address\CityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $zip
 * @property int $state_id
 * @property int $geo_coordinate_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class City extends Model
{
    /** @use HasFactory<CityFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Address\State, $this>
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Address\GeoCoordinate, $this>
     */
    public function geoCoordinate(): BelongsTo
    {
        return $this->belongsTo(GeoCoordinate::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Address\Street, $this>
     */
    public function streets(): HasMany
    {
        return $this->hasMany(Street::class);
    }
}
