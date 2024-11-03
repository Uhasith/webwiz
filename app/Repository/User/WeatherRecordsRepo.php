<?php

namespace App\Repository\User;

use App\Helpers\Utility;
use App\ModelsV2\WeatherRecords;
use Webpatser\Uuid\Uuid;

class WeatherRecordsRepo
{

    public function save($data,$sensorDataId): WeatherRecords
    {
        $weatherData = new WeatherRecords();
        $weatherData->id = Uuid::generate();
        $weatherData->sensor_location_id = $data['sensor_location_id'];
        $weatherData->status = Utility::$statusActive;
        $weatherData->humidity = $data['humidity'];
        $weatherData->wind = $data['wind'];
        $weatherData->pressure = $data['pressure'];
        $weatherData->temperature = $data['temperature'];
        $weatherData->cloud = $data['cloud'];
        $weatherData->precipitation = $data['precipitation'];
        $weatherData->sensor_data_id = $sensorDataId;
        $weatherData->save();
        return $weatherData;
    }

    public function saveWeatherList(array $weatherData){
        return WeatherRecords::insert($weatherData);
    }

}
