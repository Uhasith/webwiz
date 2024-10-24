<?php

namespace App\Repository\User;

use App\Helpers\Utility;
use App\ModelsV2\MonthlySensorData;

class MonthlySensorDataRepo{


    public function save($data)
    {
        $sensorData = new MonthlySensorData();
        $sensorData->sensor_location_id = $data['sensor_location_id'];
        $sensorData->status = $data['status'];
        $sensorData->pm2_5 = $data['pm2_5'];
        $sensorData->pm10 = $data['pm10'];
        $sensorData->o3 = $data['o3'];
        $sensorData->co = $data['co'];
        $sensorData->no2 = $data['no2'];
        $sensorData->so2 = $data['so2'];
        $sensorData->co2 = $data['co2'];
        $sensorData->pm2_5_aqi = json_encode($data['pm2_5_aqi']);
        $sensorData->pm10_aqi = json_encode($data['pm10_aqi']);
        $sensorData->o3_aqi = json_encode($data['o3_aqi']);
        $sensorData->co_aqi = json_encode($data['co_aqi']);
        $sensorData->no2_aqi = json_encode($data['no2_aqi']);
        $sensorData->so2_aqi = json_encode($data['so2_aqi']);
        $sensorData->co2_aqi = json_encode($data['co2_aqi']);
        $sensorData->aqi = json_encode($data['aqi']);
        $sensorData->save();

    }

    public function getSensorData($locationId, $equipmentId, $startTime,  $endTime)
    {

        return MonthlySensorData::whereHas('sensorLocation', function ($query) use ($locationId,$equipmentId) {
            $query->where('sensor_id', $equipmentId);
            $query->where('location_id', $locationId);
        })
            ->whereBetween('created_at', [$startTime, $endTime])
            ->with('weatherRecords')
            ->where('status',Utility::$statusActive)->orderBy('created_at','asc')->get();
    }


    public function saveMonthlySensorDataList(array $sensorData){
        return MonthlySensorData::insert($sensorData);
    }

    public function getMonthlyData($start,$end): \Illuminate\Database\Eloquent\Collection|array
    {

        return MonthlySensorData::with('weatherRecords')
        ->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])->get();

    }
    public function removeDuplicateIdentifiedData($identifier,$sensorLocationId): void
    {
        $annuallySensorDataRecords = MonthlySensorData::where('identifier',$identifier)->where('sensor_location_id',$sensorLocationId)->get();
        foreach ($annuallySensorDataRecords as $annuallySensorData) {
            $annuallySensorData->weatherRecords()->delete();
            $annuallySensorData->delete();
        }
    }
}
