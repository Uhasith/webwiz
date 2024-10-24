<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property integer $sensor_location_id
 * @property string $status
 * @property float $pm2_5
 * @property float $pm10
 * @property float $o3
 * @property float $co
 * @property float $no2
 * @property float $so2
 * @property float $co2
 * @property float $pm1
 * @property float $pm100
 * @property float $pm4
 * @property mixed $pm2_5_aqi
 * @property mixed $pm10_aqi
 * @property mixed $o3_aqi
 * @property mixed $co_aqi
 * @property mixed $no2_aqi
 * @property mixed $so2_aqi
 * @property mixed $co2_aqi
 * @property string $created_at
 * @property string $updated_at
 * @property SensorLocations $sensorLocation
 * @property float $pm2_5_status
 * @property float $pm10_status
 * @property float $o3_status
 * @property float $co_status
 * @property float $no2_status
 * @property float $so2_status
 * @property float $pm1_status
 * @property float $pm100_status
 * @property float $pm4_status
 * @property WeeklyWeatherData $weeklyWeatherData
 */
class WeeklySensorData extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['id', 'sensor_location_id', 'status', 'pm2_5', 'pm10', 'pm1', 'pm100', 'pm4', 'o3', 'co', 'no2', 'so2', 'co2', 'pm2_5_aqi', 'pm10_aqi', 'o3_aqi', 'co_aqi', 'no2_aqi', 'so2_aqi', 'co2_aqi', 'pm2_5_status', 'pm10_status', 'pm4_status', 'o3_status', 'co_status', 'no2_status', 'so2_status', 'pm1_status', 'pm100_status', 'aqi', 'created_at', 'updated_at'];

    protected $casts = [
        'pm2_5_aqi' => 'array',
        'pm10_aqi' => 'array',
        'o3_aqi' => 'array',
        'co_aqi' => 'array',
        'no2_aqi' => 'array',
        'so2_aqi' => 'array',
        'co2_aqi' => 'array',
        'aqi' => 'array'

    ];

    protected $keyType = 'string';
    public $incrementing = false;


    /**
     * @return BelongsTo
     */
    public function sensorLocation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\SensorLocations');
    }

    /**
     * @return HasOne
     */
    public function weatherRecords(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\ModelsV2\WeeklyWeatherData');
    }


}
