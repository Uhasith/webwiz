<?php

namespace App\Repository\Admin;

use App\Helpers\Utility;
use App\ModelsV2\AnuallySensorData;
use App\ModelsV2\DailySensorData;
use App\ModelsV2\FifteenMinuteSensorData;
use App\ModelsV2\HourlySensorData;
use App\ModelsV2\MonthlySensorData;
use App\ModelsV2\SensorDatas;
use App\ModelsV2\WeeklySensorData;

class HistoricalDataRepo
{
    public function getAll($data,$user)
    {
        $query = HourlySensorData::where('type',Utility::$typeManual)->whereHas('sensorLocation', function ($query) use ($data,$user) {
            if (!$user->hasRole(Utility::$superAdmin)) {
                $organizationIds = $user->userOrganizations()->pluck('organization_id')->toArray();
                $query->whereIn('organization_id', $organizationIds);
            }
            $query->whereHas('location', function ($query) use ($data) {
                if (!empty($data['searchTerm'])) {
                    $query->where('name', 'like', "%{$data['searchTerm']}%");
                }
            });
            $query->whereHas('sensor', function ($query) use ($data) {
                if (!empty($data['selectedEquipmentType'])) {
                    $query->where('equipment_type_id', '=', $data['selectedEquipmentType']);
                }
                if (!empty($data['selectedSensors'])) {
                    $query->where('id', '=', $data['selectedSensors']);
                }

            });
        })->with([
            'sensorLocation.location:id,name',
            'sensorLocation.sensor:id,equipment_type_id'
        ])
        ->with('weatherRecords');

        $query->where(function ($query) use ($data) {
            if ($data['start'] && $data['end']) {
                $query->whereBetween('created_at', [$data['start'], $data['end']]);
            }
            if (!empty($data['selectedDate'])) {
                $query->where('created_at', 'like', "%{$data['selectedDate']}%");
            }
            if (!empty($data['selectedStatus']) && $data['selectedStatus'] !== 'ALL') {
                $query->where('status', '=', $data['selectedStatus']);
            }
        });

        return $query;
    }

    public function lastUpdatedRecord($type){
        if($type == Utility::$minute){
            return FifteenMinuteSensorData::where('type',Utility::$typeManual)->latest('updated_at')
                ->value('updated_at');
        }
        if($type == Utility::$weekly){
            return WeeklySensorData::where('type',Utility::$typeManual)->latest('updated_at')
                ->value('updated_at');
        }
        if($type == Utility::$hour){
            return HourlySensorData::where('type',Utility::$typeManual)->latest('updated_at')
                ->value('updated_at');
        }
        if($type == Utility::$daily){
            return DailySensorData::where('type',Utility::$typeManual)->latest('updated_at')
                ->value('updated_at');
        }
        if($type == Utility::$monthly){
            return MonthlySensorData::where('type',Utility::$typeManual)->latest('updated_at')
                ->value('updated_at');
        }
        if($type == Utility::$annually){
            return AnuallySensorData::where('type',Utility::$typeManual)->latest('updated_at')
                ->value('updated_at');
        }
        return SensorDatas::where('type',Utility::$typeManual)->latest('updated_at')
            ->value('updated_at');

    }
}
