<?php

namespace App\Repository\User;

use App\Helpers\Utility;
use App\ModelsV2\FifteenMinuteWeatherData;
use Webpatser\Uuid\Uuid;

class FifteenMinuteWeatherRepo
{

    public  function save(array $data, $fifteen_minute_sensor_data_id)
    {

        $id = Uuid::generate() . "";
        $fifteenMinute = new FifteenMinuteWeatherData();
        $fifteenMinute->id = $id;
        $fifteenMinute->sensor_location_id = $data['sensor_location_id'];
        $fifteenMinute->status = Utility::$statusActive;
        $fifteenMinute->humidity = $data['humidity'] ?? null;
        $fifteenMinute->wind = $data['wind'] ?? null;
        $fifteenMinute->pressure = $data['pressure'] ?? null;
        $fifteenMinute->temperature = $data['temperature'] ?? null;
        $fifteenMinute->cloud = $data['cloud'] ?? null;
        $fifteenMinute->precipitation = $data['precipitation'] ?? null;
        $fifteenMinute->fifteen_minute_sensor_data_id = $fifteen_minute_sensor_data_id;
        $fifteenMinute->save();
        return $fifteenMinute;

    }

    public function saveFifteenMinuteWeatherList(array $weatherData){
        return FifteenMinuteWeatherData::insert($weatherData);
    }
}
