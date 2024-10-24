<?php

namespace App\Service\User;

use App\AqiService;
use App\Repository\User\AnuallySensorDataRepo;
use App\Repository\User\DailySensorDataRepo;
use App\Repository\User\HourlySensorDataRepo;
use App\Repository\User\MonthlySensorDataRepo;
use App\Repository\User\SensorDataRepo;
use App\Repository\User\WeeklySensorDataRepo;

class SensorDataService
{

    private AnuallySensorDataRepo $annuallySensorRepo;
    private DailySensorDataRepo $dailySensorRepo;
    private MonthlySensorDataRepo $monthlySensorRepo;
    private HourlySensorDataRepo $hourlySensorRepo;
    private SensorDataRepo $sensorDataRepo;
    private AqiService $aqiService;
    private WeeklySensorDataRepo $weeklySensorDataRepo;


    public function __construct(AnuallySensorDataRepo $annuallySensorRepo, DailySensorDataRepo $dailySensorRepo, MonthlySensorDataRepo $monthlySensorRepo, HourlySensorDataRepo $hourlySensorRepo, SensorDataRepo $sensorDataRepo, WeeklySensorDataRepo $weeklySensorDataRepo, AqiService $aqiService)
    {

        $this->annuallySensorRepo = $annuallySensorRepo;
        $this->dailySensorRepo = $dailySensorRepo;
        $this->monthlySensorRepo = $monthlySensorRepo;
        $this->hourlySensorRepo = $hourlySensorRepo;
        $this->sensorDataRepo = $sensorDataRepo;
        $this->aqiService = $aqiService;
        $this->weeklySensorDataRepo = $weeklySensorDataRepo;
    }

    public function getSensorData($locationId, $equipmentId, $startTime, $endTime)
    {

        if (!$startTime && !$endTime) {

            return [
                "type" => "latest",
                "result" => $this->sensorDataRepo->getSensorData($locationId, $equipmentId),
                "today" => $this->sensorDataRepo->getSensorData($locationId, $equipmentId, true)
                    ?? $this->sensorDataRepo->getSensorData($locationId, $equipmentId, false, true)
            ];
        }
        $interval = $startTime->diff($endTime);

        if ($interval->days <= 1) {

            return [
                "type" => "hourly",
                "result" => $this->hourlySensorRepo->getSensorData($locationId, $equipmentId, $startTime, $endTime)
            ];

        } elseif ($interval->days <= 7) {

            $daily = $this->dailySensorRepo->getSensorData($locationId, $equipmentId, $startTime, $endTime);
            if (!$daily) {
                return [
                    "type" => "hourly",
                    "result" => $this->hourlySensorRepo->getSensorData($locationId, $equipmentId, $startTime, $endTime)
                ];
            }

            return [
                "type" => "daily",
                "result" => $daily
            ];
        } elseif ($interval->days <= 30) {
            $weekly = $this->weeklySensorDataRepo->getSensorData($locationId, $equipmentId, $startTime, $endTime);
            if (!$weekly) {
                return [
                    "type" => "daily",
                    "result" => $this->dailySensorRepo->getSensorData($locationId, $equipmentId, $startTime, $endTime)
                ];
            }
            return [
                "type" => "weekly",
                "result" => $weekly
            ];
        } elseif ($interval->days <= 365) {

            $monthly = $this->monthlySensorRepo->getSensorData($locationId, $equipmentId, $startTime, $endTime);
            if (!$monthly) {
                return [
                    "type" => "weekly",
                    "result" => $this->weeklySensorDataRepo->getSensorData($locationId, $equipmentId, $startTime, $endTime)
                ];
            }
            return [
                "type" => "monthly",
                "result" => $monthly
            ];

        } else {

            $annually = $this->annuallySensorRepo->getSensorData($locationId, $equipmentId, $startTime, $endTime);
            if (!$annually) {
                return [
                    "type" => "monthly",
                    "result" => $this->monthlySensorRepo->getSensorData($locationId, $equipmentId, $startTime, $endTime)
                ];
            }
            return [
                "type" => "annually",
                "result" => $annually
            ];
        }

    }

    public function getRecentData($sensorId, $equipmentId, $sensorLocationId, $ranking): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->sensorDataRepo->getDataFromLastFiveMinutes($sensorId, $equipmentId, $sensorLocationId, $ranking);
    }

    public function getDefaultLocationRecentData($sensorId, $equipmentId, $sensorLocationId)
    {
        return $this->sensorDataRepo->getDefaultLocationRecentData($sensorId, $equipmentId, $sensorLocationId);
    }

    public function saveSensorData($sensorData)
    {
        $processedData = $this->aqiService->processAirQualityData($sensorData, null);
        return $this->sensorDataRepo->save($processedData);
    }

}
