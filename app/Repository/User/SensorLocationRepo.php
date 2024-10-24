<?php

namespace App\Repository\User;

use App\ModelsV2\SensorLocations;

class SensorLocationRepo{

    public function getById($sensorId){

        return SensorLocations::with('location')
        ->where('id', $sensorId)
        ->first();
    }

    public function getSensorLocationIds()
    {
        return SensorLocations::pluck('id');
    }
    public function getSensorLocationBySensorAndLocation($sensorId, $locationId)
    {
        return SensorLocations::where('sensor_id',$sensorId)->where('location_id',$locationId)->first();
    }

}
