<?php

namespace App\Repository\User;

use App\Helpers\Utility;
use App\ModelsV2\HourlySensorData;
use App\ModelsV2\OptionalSensorData;
use Carbon\Carbon;

class HourlySensorDataRepo
{

    public function save($data): void
    {
        $sensorData = new HourlySensorData();
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

        return HourlySensorData::whereHas('sensorLocation', function ($query) use ($locationId,$equipmentId) {
            $query->where('sensor_id', $equipmentId);
            $query->where('location_id', $locationId);
        })
            ->whereBetween('created_at', [$startTime, $endTime])
            ->with('hourlyWeatherData')
            ->where('status',Utility::$statusActive)->orderBy('created_at','asc')->get();
    }

    public function saveHourlySensorDataList(array $sensorData){
        return HourlySensorData::insert($sensorData);
    }

    public function getHourData($start,$end): \Illuminate\Database\Eloquent\Collection|array
    {

        return HourlySensorData::with('weatherRecords')
        ->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])->get();

    }
    public function get24HTrendData($sensorLocationId)
    {

        $latestRecordsSubQuery = HourlySensorData::whereBetween('created_at',
            [Carbon::now()->subDay()->startOfDay(), Carbon::now()->startOfDay()]
        )
            ->where('status', Utility::$statusActive)
            ->where('sensor_location_id', $sensorLocationId);

        if (!empty($sensorIds) && !in_array('all', $sensorIds)) {
            $latestRecordsSubQuery->whereHas('sensorLocation.sensor', function ($q) use ($sensorIds) {
                $q->whereIn('id', $sensorIds);
            });
        }
        return $latestRecordsSubQuery->orderBy('created_at','asc')->get();
    }

    public function insert(array $data): void
    {
        $chunks = array_chunk($data, 500);
        foreach ($chunks as $chunk) {
            HourlySensorData::insert($chunk);
        }
    }

    public function removeDuplicateIdentifiedData($identifier,$sensorLocationId): void
    {
        $hourlySensorDataRecords = HourlySensorData::where('identifier',$identifier)->where('sensor_location_id',$sensorLocationId)->get();
        foreach ($hourlySensorDataRecords as $hourlySensorData) {
            $hourlySensorData->weatherRecords()->delete();
            OptionalSensorData::where('hourly_sensor_data_id',$hourlySensorData->id)->delete();
//            OptionalSensorData::where('sensor_data_id',$hourlySensorData->id)->delete();
            $hourlySensorData->delete();
        }
    }
}
