<?php

namespace App\Repository\User;

use App\Helpers\Utility;
use App\ModelsV2\AnuallySensorData;
use Illuminate\Support\Facades\Log;

class AnuallySensorDataRepo
{

    public function save($data): void
    {
        $sensorData = new AnuallySensorData();
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

        return AnuallySensorData::whereHas('sensorLocation', function ($query) use ($locationId,$equipmentId) {
            $query->where('sensor_id', $equipmentId);
            $query->where('location_id', $locationId);
        })
            ->whereBetween('created_at', [$startTime, $endTime])
            ->with('anuallyWeatherData')
            ->where('status',Utility::$statusActive)->orderBy('created_at','asc')->get();
    }

    public function saveAnuallySensorDataList(array $sensorData){
        return AnuallySensorData::insert($sensorData);
    }

    public function removeDuplicateIdentifiedData($identifier,$sensorLocationId): void
    {
        $annuallySensorDataRecords = AnuallySensorData::where('identifier',$identifier)->where('sensor_location_id',$sensorLocationId)->get();
        foreach ($annuallySensorDataRecords as $annuallySensorData) {
            $annuallySensorData->anuallyWeatherData()->delete();
            $annuallySensorData->delete();
        }
    }
}
