<?php

namespace App\Service\User\Equipments;

use App\Repository\User\EchoTechSensorDataRepo;
use App\Repository\User\SensorLocationRepo;
use App\Repository\User\SensorsRepo;
use App\Repository\User\WeatherRecordsRepo;
use App\Service\User\WeatherService;
use Illuminate\Support\Facades\Log;

class EquipmentFactory
{
    public function getInstance(): EquipmentService|EquipmentMockService
    {
        $sensorRepo = new SensorsRepo();
        $weatherRecordsRepo = new WeatherRecordsRepo();
        $sensorLocationRepo = new SensorLocationRepo();
    
        $weatherService = new WeatherService($weatherRecordsRepo,$sensorLocationRepo);
        if (env("EQUIPMENT_MOCK")) {
            Log::info("Equipment Mock Enabled");
            return new EquipmentMockService($sensorRepo);
        }
        Log::info("Equipment Service Enabled");
        return new EquipmentService($sensorRepo,$weatherService);
    }
}
