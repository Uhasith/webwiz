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
 * @property string $fifteen_minute_sensor_data_id
 * @property string $status
 * @property float $humidity
 * @property float $wind
 * @property float $pressure
 * @property float $temperature
 * @property string $cloud
 * @property string $created_at
 * @property string $updated_at
 * @property SensorLocations $sensorLocation
 * @property FifteenMinuteData $fifteenMinuteData
 * @property mixed|null $precipitation
 */

class FifteenMinuteWeatherData extends Model{

    /**
     * @var array
     */
    protected $fillable = ['id','sensor_location_id','fifteen_minute_sensor_data_id', 'status', 'humidity', 'wind', 'pressure', 'temperature','cloud','created_at', 'updated_at'];

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * @return BelongsTo
     */
    public function sensorLocation(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\SensorLocations');
    }

    public function fifteenMinuteData(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\FifteenMinuteData');
    }

    public static function formatWeatherData(array $data,$fifteen_minute_sensor_data_id ): array
    {
        $id = Uuid::generate() . "";

        return [
            'id' => $id,
            'sensor_location_id' => $data['sensorLocationId'],
            'status' => Utility::$statusActive,
            'humidity' => $data['humidity']??null,
            'wind' => $data['wind']??null,
            'pressure' => $data['pressure']??null,
            'temperature' => $data['temperature']??null,
            'cloud' => $data['cloud']??null,
            'precipitation' => $data['precipitation']??null,
            'fifteen_minute_sensor_data_id'=> $fifteen_minute_sensor_data_id,
            'created_at' => Carbon::now()->subMinute()->endOfMinute(),
            'updated_at' => Carbon::now()->subMinute()->endOfMinute()
        ];
    }


}
