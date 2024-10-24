<?php

namespace App\Repository\User;


use App\Helpers\Utility;
use App\ModelsV2\MonthlyWeatherData;


class MonthlyWeatherDataRepo
{

    public function saveMonthlyWeatherList(array $weatherData){

        return MonthlyWeatherData::insert($weatherData);
    }
    public function getWeatherData($locationId,$equipmentId,$startTime, $endTime){
        return MonthlyWeatherData::whereHas('sensorLocation', function ($query) use ($locationId,$equipmentId) {
            $query->where('sensor_id', $equipmentId);
            $query->where('location_id', $locationId);
        })
            ->whereBetween('created_at', [$startTime, $endTime])
            ->where('status',Utility::$statusActive)->orderBy('created_at','asc')->get();
    }
}
