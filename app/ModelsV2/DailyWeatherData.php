<?php

namespace App\ModelsV2;

use App\Helpers\Utility;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webpatser\Uuid\Uuid;

/**
 * @property string $id
 * @property integer $sensor_location_id
 * @property integer $daily_sensor_data_id
 * @property string $status
 * @property float $humidity
 * @property float $wind
 * @property float $pressure
 * @property float $temperature
 * @property string $cloud
 * @property string $created_at
 * @property string $updated_at
 * @property SensorLocations $sensorLocation
 * @property DailySensorData $dailySensorData
 */

class DailyWeatherData extends Model{

    /**
     * @var array
     */
    protected $fillable = ['id','sensor_location_id','daily_sensor_data_id', 'status', 'humidity', 'wind', 'pressure', 'temperature','cloud','created_at', 'updated_at'];

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sensorLocation()
    {
        return $this->belongsTo('App\ModelsV2\SensorLocations');
    }

    /**
     * @return BelongsTo
     */
    public function dailySensorData(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\DailySensorData');
    }

    /**
     * @param array $data
     * @param $daily_sensor_data_id
     * @return array
     * @throws \Exception
     */
    public static function formatWeatherData(array $data, $daily_sensor_data_id ): array
    {
        $id = Uuid::generate() . "";

        $formattedWeatherData = Utility::weatherDataFormat($data,$data['sensor_location_id']);
        return array_merge($formattedWeatherData,[
            'id' => $id,
            'daily_sensor_data_id'=> $daily_sensor_data_id,
            'status' => $data['status'] ?? Utility::$statusActive,
            'created_at' => $data['created_at']  ?? Carbon::now()->subHour()->endOfHour(),
            'updated_at' => $data['updated_at'] ??  Carbon::now()->subHour()->endOfHour()
        ]);
    }


}
