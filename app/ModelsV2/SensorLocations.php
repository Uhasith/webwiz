<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $sensor_id
 * @property integer $location_id
 * @property string $locationindex
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property Reports[] $reports
 * @property SensorDatas[] $sensorDatas
 * @property Sensors $sensor
 * @property Locations $location
 * @property SensorLogs[] $sensorLogs
 * @property WeatherRecords[] $weatherRecords
 */
class SensorLocations extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['sensor_id','latitude', 'longitude', 'location_id','locationindex', 'status', 'created_at', 'updated_at'];

    /**
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany('App\ModelsV2\Reports');
    }

    /**
     * @return HasMany
     */
    public function sensorDatas(): HasMany
    {
        return $this->hasMany('App\ModelsV2\SensorDatas','sensor_location_id');
    }

    /**
     * @return HasMany
     */
    public function monthlySensorData(): HasMany
    {
        return $this->hasMany('App\ModelsV2\MonthlySensorData','sensor_location_id');
    }

    /**
     * @return HasMany
     */
    public function annuallySensorData(): HasMany
    {
        return $this->hasMany('App\ModelsV2\AnuallySensorData','sensor_location_id');
    }

    /**
     * @return BelongsTo
     */
    public function sensor(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\Sensors');
    }

    /**
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\Locations');
    }

    /**
     * @return HasMany
     */
    public function sensorLogs(): HasMany
    {
        return $this->hasMany('App\ModelsV2\SensorLogs');
    }

    /**
     * @return HasMany
     */
    public function weatherRecords(): HasMany
    {
        return $this->hasMany('App\ModelsV2\WeatherRecords', 'sensor_location_id');
    }

    public function optionalSensorDatas(): HasMany
    {
        return $this->hasMany('App\ModelsV2\OptionalSensorData');
    }

}
