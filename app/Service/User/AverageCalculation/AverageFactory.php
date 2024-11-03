<?php

namespace App\Service\User\AverageCalculation;

use App\Repository\User\DailySensorDataRepo;
use App\Repository\User\HourlySensorDataRepo;
use App\Repository\User\MonthlySensorDataRepo;
use App\Repository\User\SensorDataRepo;
use App\Repository\User\SensorLocationRepo;
use App\Repository\User\WeeklySensorDataRepo;
use Illuminate\Support\Facades\Log;

class AverageFactory
{
    /**
     * @return AverageService|AverageMockService
     */
    public function getInstance(): AverageService|AverageMockService
    {
        $sensorRepo = new SensorDataRepo();
        $hourlyRepo = new HourlySensorDataRepo();
        $dailyRepo = new DailySensorDataRepo();
        $weeklyRepo = new WeeklySensorDataRepo();
        $monthlyRepo = new MonthlySensorDataRepo();

        $sensorLocationRepo = new SensorLocationRepo();
        if (env("EQUIPMENT_MOCK")) {
            Log::info("Average Mock Enabled");
            return new AverageMockService($sensorLocationRepo);
        }
        return new AverageService($sensorRepo,$hourlyRepo,$dailyRepo,$weeklyRepo,$monthlyRepo);
    }
}
