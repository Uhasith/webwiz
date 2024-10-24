<?php

namespace App\Repository\Admin;

use App\ModelsV2\Sensors;
use App\ModelsV2\SensorDatas;
use App\ModelsV2\SensorLocations;
use App\ModelsV2\EquipmentType;
use Illuminate\Support\Facades\DB;

class SensorRepo
{
    public function getPaginatedSensors($perPage = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Sensors::with('equipmentType')->paginate($perPage);
    }

    public function getAllSensorData(): array
    {
    $data = DB::table('sensor_datas')
        ->join('sensor_locations', 'sensor_datas.sensor_location_id', '=', 'sensor_locations.id')
        ->join('sensors', 'sensor_locations.sensor_id', '=', 'sensors.id')
        ->select(
            'sensor_datas.id as id',
            'sensor_datas.created_at as date_time',
            'sensor_locations.location_id as location_id',
            'sensor_datas.pm2_5',
            'sensor_datas.pm10',
            'sensor_datas.o3',
            'sensor_datas.co',
            'sensor_datas.no2',
            'sensor_datas.so2',
            'sensor_datas.co2',
            'sensor_datas.aqi',
            'sensor_datas.status'
        )
        ->get();

    $totalCount = $data->count();
    $activeCount = $data->where('status', 'ACTIVE')->count();

    $mappedData = $data->map(function ($item) {
        $location = $this->getLocationNameById($item->location_id);

        return [
            'id' => $item->id,
            'date_time' => $item->date_time,
            'location' => $location,
            'pm2_5' => $item->pm2_5,
            'pm10' => $item->pm10,
            'o3' => $item->o3,
            'co' => $item->co,
            'no2' => $item->no2,
            'so2' => $item->so2,
            'voc' => 'N/A',
            'co2' => $item->co2,
            'status' => $item->status,
        ];
    });

    return [
        'data' => $mappedData,
        'total_count' => $totalCount,
        'active_count' => $activeCount
    ];
}


    private function getLocationNameById($locationId)
    {
        $sensorLocation = SensorLocations::with('location')->find($locationId);

        if ($sensorLocation && $sensorLocation->location) {
            return $sensorLocation->location->name;
        }

        return 'Unknown Location';
    }

    public function getEquipmentsWithCount(): \Illuminate\Http\JsonResponse
    {
    $sensors = Sensors::select('sensors.name', 'sensors.status', 'equipment_types.type_name', 'organization.name as organization_name')
    ->join('equipment_types', 'sensors.equipment_type_id', '=', 'equipment_types.id')
    ->join('sensor_organization', 'sensors.id', '=', 'sensor_organization.sensor_id')
    ->join('organization', 'sensor_organization.organization_id', '=', 'organization.id')
    ->get();

    $sensorLocations = \DB::table('sensors')
    ->select('sensors.name as sensor_name', 'locations.name as location_name', 'sensor_locations.status as sensor_locations_status', 'sensor_locations.updated_at as updated_at', 'organization.name as organization_name')
    ->join('sensor_locations', 'sensors.id', '=', 'sensor_locations.sensor_id')
    ->join('locations', 'sensor_locations.location_id', '=', 'locations.id')
    ->join('sensor_organization', 'sensors.id', '=', 'sensor_organization.sensor_id')
    ->join('organization', 'sensor_organization.organization_id', '=', 'organization.id')
    ->get();

    $totalSensors = Sensors::count();
    $totalEquipmentTypes = EquipmentType::count();
    $activeSensors = Sensors::where('status', 'active')->count();
    $inactiveSensors = Sensors::where('status', 'inactive')->count();

    return response()->json([
        'sensors' => $sensors,
        'sensorLocations' => $sensorLocations,
        'totalSensors' => $totalSensors,
        'totalEquipmentTypes' => $totalEquipmentTypes,
        'activeSensors' => $activeSensors,
        'inactiveSensors' => $inactiveSensors,
    ]);
}
    public function findSensorData($id){
        return SensorDatas::findOrFail($id);
    }

    public function findSensorById($sensorId){
        return Sensors::where('id',$sensorId)->first();
    }

}
