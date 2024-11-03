<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property integer $id
 * @property integer $sensor_location_id
 * @property string $status
 * @property float $pm2_5
 * @property float $pm10
 * @property float $o3
 * @property float $co
 * @property float $no2
 * @property float $so2
 * @property float $co2
 * @property mixed $pm2_5_aqi
 * @property mixed $pm10_aqi
 * @property mixed $o3_aqi
 * @property mixed $co_aqi
 * @property mixed $no2_aqi
 * @property mixed $so2_aqi
 * @property mixed $co2_aqi
 * @property mixed $aqi
 * @property string $created_at
 * @property string $updated_at
 * @property SensorLocations $sensorLocation
 * @property DailyWeatherData $dailyWeatherData
 *
 */
class DailySensorData extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_sensor_data';

    /**
     * @var array
     */
    protected $fillable = ['sensor_location_id', 'status', 'pm2_5', 'pm10', 'o3', 'co', 'no2', 'so2', 'co2', 'pm2_5_aqi', 'pm10_aqi', 'o3_aqi', 'co_aqi', 'no2_aqi', 'so2_aqi', 'co2_aqi', 'aqi', 'created_at', 'updated_at'];


    protected $casts = [
        'pm2_5_aqi' => 'array',
        'pm10_aqi' => 'array',
        'o3_aqi' => 'array',
        'co_aqi' => 'array',
        'no2_aqi' => 'array',
        'so2_aqi' => 'array',
        'co2_aqi' => 'array',
         'aqi'=>'array'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * @return BelongsTo
     */
    public function sensorLocation(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\SensorLocations');
    }

    /**
     * @return HasOne
     */
    public function weatherRecords(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\ModelsV2\DailyWeatherData');
    }

    // Direct relationship to Location through SensorLocation
    public function location()
    {
        return $this->hasOneThrough(Locations::class, SensorLocations::class, 'id', 'id', 'sensor_location_id', 'location_id');
    }

    // Direct relationship to Sensor through SensorLocation
    public function sensor()
    {
        return $this->hasOneThrough(Sensors::class, SensorLocations::class, 'id', 'id', 'sensor_location_id', 'sensor_id');
    }

}
