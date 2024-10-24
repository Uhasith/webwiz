<?php

namespace App\Repository\User;

use App\Helpers\Utility;
use App\ModelsV2\AnuallyWeatherData;



class AnuallyWeatherDataRepo
{

    public function saveAnuallyWeatherList(array $weatherData){

        return AnuallyWeatherData::insert($weatherData);
    }

    public function getWeatherData($locationId,$equipmentId,$startTime, $endTime){
        return AnuallyWeatherData::whereHas('sensorLocation', function ($query) use ($locationId,$equipmentId) {
            $query->where('sensor_id', $equipmentId);
            $query->where('location_id', $locationId);
        })
            ->whereBetween('created_at', [$startTime, $endTime])
            ->where('status',Utility::$statusActive)->orderBy('created_at','asc')->get();
    }

}
