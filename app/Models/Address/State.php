<?php

declare(strict_types=1);

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $iso_code
 * @property string $rs_code
 * @property string $country_iso_code
 */
class State extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Address\City, $this>
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
