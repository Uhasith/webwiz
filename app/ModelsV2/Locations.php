<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $district_id
 * @property float $latitude
 * @property float $longitude
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property Districts $district
 * @property SensorLocations[] $sensorLocations
 *
 */
class Locations extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['district_id', 'latitude', 'longitude', 'name','created_at', 'updated_at'];

    /**
     * @return BelongsTo
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\Districts');
    }

    /**
     * @return HasMany
     */
    public function sensorLocations(): HasMany
    {
        return $this->hasMany('App\ModelsV2\SensorLocations','location_id');
    }
}
