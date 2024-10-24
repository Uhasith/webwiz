<?php

namespace App\Service\User;

use App\AqiService;
use Illuminate\Support\Facades\Log;
use App\Repository\User\SensorsRepo;
use App\Repository\User\SensorDataRepo;
use App\Repository\User\EquipmentTypeRepo;
use App\Http\Resources\EquipmentTypeResource;
use App\Repository\User\HourlySensorDataRepo;

class SensorsService
{
    private SensorsRepo $sensorRepository;
    private SensorDataRepo $sensorDataRepo;
    private EquipmentTypeRepo $equipmentTypeRepo;
    private HourlySensorDataRepo $hourlySensorDataRepo;

    private $aqiService;

    /**
     * @param SensorsRepo $sensorRepository
     * @param SensorDataRepo $sensorDataRepo
     * @param HourlySensorDataRepo $hourlySensorDataRepo
     * @param EquipmentTypeRepo $equipmentTypeRepo
     */
    public function __construct(
        SensorsRepo $sensorRepository,
        SensorDataRepo $sensorDataRepo,
        HourlySensorDataRepo $hourlySensorDataRepo,
        EquipmentTypeRepo $equipmentTypeRepo,
        AqiService $aqiService,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->sensorDataRepo = $sensorDataRepo;
        $this->equipmentTypeRepo = $equipmentTypeRepo;
        $this->hourlySensorDataRepo = $hourlySensorDataRepo;
        $this->aqiService = $aqiService;
    }

    public function homepageService(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $equipmentTypes = $this->equipmentTypeRepo->getAllEquipmentTypes();
        return EquipmentTypeResource::collection($equipmentTypes);
    }

    public function getSensorsByEquipmentId($id)
    {
        if ($id === 'all') {
            return $this->sensorRepository->getAll();
        } else {
            return $this->sensorRepository->getSensorsByEquipmentId($id);
        }
    }

    /**
     * @param $sensorLocationId
     * @return mixed
     */
    public function get24HTrendData($sensorLocationId)
    {
        return $this->hourlySensorDataRepo->get24HTrendData($sensorLocationId);
    }

    public function getSensorsByLocationId($locationId)
    {
        return $this->sensorRepository->getSensorsByLocationsId($locationId);
    }


    public function saveSensorDataList(array $sensorData)
    {
        return $this->sensorDataRepo->saveSensorDataList($sensorData);
    }

    public function updateSensorData(array $sensorData)
    {
        $data = [
            "PM2.5" => $sensorData['pm2_5'],
            "PM10" => $sensorData['pm10'],
            "O3" => $sensorData['o3'],
            "NO2" => $sensorData['no2'],
            "SO2" => $sensorData['so2'],
            "CO" => $sensorData['co'],
            "sensor_location_id" => $sensorData['sensor_location_id'],
        ];

        $formattedData = $this->aqiService->processAirQualityData($data, null);

        $formattedData['sensor_data_id'] = $sensorData['sensor_data_id'];
        $formattedData['co2'] = $sensorData['co2'];

        unset($formattedData['created_at']);
        unset($formattedData['identifier']);
        unset($formattedData['type']);
        unset($formattedData['id']);

        return $this->sensorDataRepo->updateSensorData($formattedData);
    }

    public function updateSensorDataStatus(array $sensorData)
    {
        return $this->sensorDataRepo->updateSensorDataStatus($sensorData);
    }

}
