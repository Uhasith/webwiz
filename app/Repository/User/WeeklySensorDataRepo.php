<?php

namespace App\Repository\User;
use App\Helpers\Utility;
use App\ModelsV2\WeeklySensorData;


class WeeklySensorDataRepo{

    public function saveWeeklySensorDataList(array $sensorData){
        return WeeklySensorData::insert($sensorData);
    }

    public function getWeeklyData($start,$end): \Illuminate\Database\Eloquent\Collection|array
    {

        return WeeklySensorData::with('weatherRecords')
        ->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])->get();

    }
    public function getSensorData($locationId, $equipmentId, $startTime,  $endTime)
    {

        return WeeklySensorData::whereHas('sensorLocation', function ($query) use ($locationId,$equipmentId) {
            $query->where('sensor_id', $equipmentId);
            $query->where('location_id', $locationId);
        })
            ->whereBetween('created_at', [$startTime, $endTime])
            ->with('weatherRecords')
            ->where('status',Utility::$statusActive)->orderBy('created_at','asc')->get();
    }
}
