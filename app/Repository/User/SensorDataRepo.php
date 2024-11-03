<?php

namespace App\Repository\User;

use Carbon\Carbon;
use App\Helpers\Utility;
use App\ModelsV2\DailySensorData;
use App\ModelsV2\HourlySensorData;
use App\ModelsV2\MonthlySensorData;
use App\ModelsV2\SensorDatas;
use App\ModelsV2\WeeklySensorData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SensorDataRepo
{

    public function save($data)
    {
        return SensorDatas::create($data);
    }

    public function getDataFromLastFiveMinutes(array $sensorIds, array $equipmentIds, $sensorLocationId, $ranking): \Illuminate\Database\Eloquent\Collection|array
    {

        $latestRecordsSubQuery = SensorDatas::select('sensor_location_id', DB::raw('MAX(created_at) as latest_created_at'))
            ->whereBetween('created_at', [Carbon::now()->subDay(), Carbon::now()])
            ->where('status', Utility::$statusActive)
            ->groupBy('sensor_location_id');

        $query = SensorDatas::with([
            'sensorLocation.sensor',
            'sensorLocation.location',
            'sensorLocation.location.district.province',
            'weatherRecords' => function ($query) {
                $query->where('status', Utility::$statusActive);
            }
        ])->joinSub($latestRecordsSubQuery, 'latest_records', function ($join) {
            $join->on('sensor_datas.sensor_location_id', '=', 'latest_records.sensor_location_id')
                ->on('sensor_datas.created_at', '=', 'latest_records.latest_created_at');
        })
            ->where('sensor_datas.status', Utility::$statusActive)
            ->orderBy('sensor_datas.sensor_location_id', 'desc');

        if ($sensorLocationId !== null) {
            $query->where('sensor_location_id', $sensorLocationId);
        }
        if ($ranking !== 'all') {
            $query->where('aqi->SL', ucfirst($ranking));
        }
        if (!empty($sensorIds) && !in_array('all', $sensorIds)) {
            $query->whereHas('sensorLocation.sensor', function ($q) use ($sensorIds) {
                $q->whereIn('id', $sensorIds);
            });
        }

        if (!empty($sensorIds) && !in_array('all', $equipmentIds)) {
            $query->whereHas('sensorLocation.sensor', function ($q) use ($equipmentIds) {
                $q->whereIn('equipment_type_id', $equipmentIds);
            });
        }
        return $query->orderBy('sensor_datas.sensor_location_id', 'desc')->get();
    }
    public function getDefaultLocationRecentData(array $sensorIds, array $equipmentIds, $sensorLocationId)
    {
        return SensorDatas::where('sensor_location_id', 1)
            ->whereBetween('created_at', [Carbon::now()->subDay(), Carbon::now()])
            ->where('status', Utility::$statusActive)
            ->with([
                'sensorLocation.sensor',
                'sensorLocation.location',
                'sensorLocation.location.district.province',
                'weatherRecords' => function ($query) {
                    $query->where('status', Utility::$statusActive);
                }
            ])->orderBy('created_at', 'desc')->first();
    }

    public function saveSensorDataList(array $sensorData)
    {

        return SensorDatas::insert($sensorData);
    }

    public function checkSensorDataDuplicate($identifier)
    {

        return SensorDatas::where('identifier', $identifier)->exists();
    }


    public function getSensorDataInRange($start, $end): \Illuminate\Database\Eloquent\Collection|array
    {

        return SensorDatas::with('weatherRecords')
            ->whereBetween('created_at', [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')])->get();
    }
    public function getSensorData($locationId, $equipmentId, $thisYear = false,$daily = false,$start = null,$end = null)
    {
        if($thisYear){
            return MonthlySensorData::whereHas('sensorLocation', function ($query) use ($locationId,$equipmentId) {
                $query->where('sensor_id', $equipmentId);
                $query->where('location_id', $locationId);
            })
                ->with('sensorLocation.location')
                ->whereBetween('created_at', [Carbon::now()->startOfYear()->format('Y-m-d H:i:s'),Carbon::now()->endOfMonth()->format('Y-m-d H:i:s')])
                ->where('status',Utility::$statusActive)->orderBy('created_at','asc')->get();
        }
        if($daily){
            return DailySensorData::whereHas('sensorLocation', function ($query) use ($locationId,$equipmentId) {
                $query->where('sensor_id', $equipmentId);
                $query->where('location_id', $locationId);
            })
                ->with('sensorLocation.location')
                ->whereBetween('created_at', [Carbon::now()->startOfMonth()->format('Y-m-d H:i:s'),Carbon::now()->endOfMonth()->format('Y-m-d H:i:s')])
                ->where('status',Utility::$statusActive)->orderBy('created_at','asc')->get();
        }
        if($start && $end){
            return SensorDatas::whereHas('sensorLocation', function ($query) use ($locationId, $equipmentId) {
                $query->where('sensor_id', $equipmentId);
                $query->where('location_id', $locationId);
            })
                ->with('sensorLocation.location')
                ->with('weatherRecords')
                ->whereBetween('created_at', [$start, $end])
                ->where('status', Utility::$statusActive)->get();
        }

        return SensorDatas::whereHas('sensorLocation', function ($query) use ($locationId, $equipmentId) {
            $query->where('sensor_id', $equipmentId);
            $query->where('location_id', $locationId);
        })
            ->with('sensorLocation.location')
            ->with('weatherRecords')
            ->where('status', Utility::$statusActive)->first();
    }

    public function updateSensorData($data)
    {
        $sensorData = SensorDatas::find($data['sensor_data_id']);

        if (!$sensorData) {
            return response()->json(['status' => 'error', 'message' => 'Sensor data not found'], 404);
        }

        unset($data['sensor_data_id']);

        // Update the sensor data
        $sensorData->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Sensor Data updated successfully',
            'data' => $sensorData
        ], 200);
    }

    public function updateSensorDataStatus($data)
    {
        $sensorData = SensorDatas::find($data['sensor_data_id']);

        if (!$sensorData) {
            return response()->json(['status' => 'error', 'message' => 'Sensor data not found'], 404);
        }

        unset($data['sensor_data_id']);

        Log::info($data);

        // Update the sensor data
        $sensorData->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Sensor Data Status updated successfully',
            'data' => $sensorData
        ], 200);
    }
}
