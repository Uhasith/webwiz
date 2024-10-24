<?php

namespace App\ModelsV2;

use App\Helpers\Utility;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Webpatser\Uuid\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property Reports[] $reports
 * @property SensorLocations $sensorLocation
 * @property WeatherRecords $weatherRecords
 * @property float $pm2_5_status
 * @property float $pm10_status
 * @property float $o3_status
 * @property float $co_status
 * @property float $no2_status
 * @property float $so2_status
 * @property float $pm1_status
 * @property float $pm100_status
 * @property float $pm4_status
 */
class SensorDatas extends Model
{
    /**
     * @var array
     */
    use SoftDeletes;

    protected $fillable = ['id', 'sensor_location_id', 'status', 'pm2_5', 'pm10', 'pm1', 'pm100', 'pm4', 'o3', 'co', 'no2', 'so2', 'co2', 'pm2_5_aqi', 'pm10_aqi', 'o3_aqi', 'co_aqi', 'no2_aqi', 'so2_aqi', 'co2_aqi', 'pm2_5_status', 'pm10_status', 'pm4_status', 'o3_status', 'co_status', 'no2_status', 'so2_status', 'pm1_status', 'pm100_status', 'aqi', 'created_at', 'updated_at', 'deleted_at','type','identifier'];

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
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany('App\ModelsV2\Reports');
    }

    /**
     * @return BelongsTo
     */
    public function sensorLocation(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\SensorLocations');
    }

    /**
     * @return HasMany
     */
    public function optionalSensorDatas(): HasMany
    {
        return $this->hasMany('App\ModelsV2\OptionalSensorData');
    }

    /**
     * @return HasOne
     */
    public function weatherRecords(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\ModelsV2\WeatherRecords','sensor_data_id');
    }

    /**
     * @param $data
     * @param $subAqis
     * @param $dataStatus
     * @param $status
     * @param $dataType
     * @return array
     * @throws \Exception
     */
    public static function sensorDataFormat($data, $subAqis, $dataStatus, $status, $dataType,$actionType): array
    {

        $createAt = Carbon::now()->toDateTimeString();
        $updatedAt = Carbon::now()->toDateTimeString();

        if($dataType == Utility::$minute){
            $createAt = Carbon::now()->subMinute()->endOfMinute();
            $updatedAt = Carbon::now();
        }else if($dataType == Utility::$hour){
            $createAt = Carbon::now()->subHour()->endOfHour();
            $updatedAt = Carbon::now();
        }else if($dataType == Utility::$history){
            $createAt = $data['created_at'];
            $updatedAt = $data['updated_at'];
        }
        $id = Uuid::generate() . "";

        $result = [
            'id' => $id,
            'sensor_location_id' => $data['sensor_location_id'],
            'status' => $status,
            'pm2_5' => $data['PM2.5'] ?? null,
            'pm10' => $data['PM10'] ?? null,
            'pm1' => $data['PM1'] ?? null,
            'pm4' => $data['PM4'] ?? null,
            'pm100' => $data['PM100'] ?? null,
            'o3' => $data['O3'] ?? null,
            'co' => $data['CO'] ?? null,
            'no2' => $data['NO2'] ?? null,
            'so2' => $data['SO2'] ?? null,
            'co2' => $data['CO2'] ?? null,
            'pm2_5_aqi' => null,
            'pm10_aqi' => null,
            'o3_aqi' => null,
            'co_aqi' => null,
            'no2_aqi' => null,
            'so2_aqi' => null,
            'co2_aqi' => null,
            'pm2_5_status' => $dataStatus['PM2.5'] ?? null,
            'pm10_status' => $dataStatus['PM10'] ?? null,
            'o3_status' => $dataStatus['O3'] ?? null,
            'co_status' => $dataStatus['CO'] ?? null,
            'no2_status' => $dataStatus['NO2'] ?? null,
            'so2_status' => $dataStatus['SO2'] ?? null,
            'aqi' => json_encode($subAqis['AQI'] ?? null),
            'type'=> $actionType ?? Utility::$typeSystem,
            'identifier'=> $data['identifier'] ?? null,
            'created_at' => $createAt,
            'updated_at' => $updatedAt,

        ];

        if (!empty($subAqis)) {
            $result['pm2_5_aqi'] = json_encode($subAqis['PM2.5'] ?? null);
            $result['pm10_aqi'] = json_encode($subAqis['PM10'] ?? null);
            $result['o3_aqi'] = json_encode($subAqis['O3'] ?? null);
            $result['co_aqi'] = json_encode($subAqis['CO'] ?? null);
            $result['no2_aqi'] = json_encode($subAqis['NO2'] ?? null);
            $result['so2_aqi'] = json_encode($subAqis['SO2'] ?? null);
            $result['co2_aqi'] = json_encode($subAqis['CO2'] ?? null);
        }

        return $result;
    }
}
