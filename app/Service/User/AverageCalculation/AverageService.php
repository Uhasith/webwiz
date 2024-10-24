<?php

namespace App\Service\User\AverageCalculation;

use App\Helpers\Utility;
use App\Repository\User\DailySensorDataRepo;
use App\Repository\User\HourlySensorDataRepo;
use App\Repository\User\MonthlySensorDataRepo;
use App\Repository\User\SensorDataRepo;
use App\Repository\User\WeeklySensorDataRepo;
use Illuminate\Support\Facades\Log;

class AverageService implements IAverageService
{

    private SensorDataRepo $sensorRepo;
    private HourlySensorDataRepo $hourlyRepo;
    private DailySensorDataRepo $dailySensorDataRepo;
    private WeeklySensorDataRepo $weeklySensorDataRepo;
    private MonthlySensorDataRepo $monthlySensorDataRepo;

    /**
     * @param SensorDataRepo $sensorRepo
     * @param HourlySensorDataRepo $hourlyRepo
     * @param DailySensorDataRepo $dailySensorDataRepo
     * @param WeeklySensorDataRepo $weeklySensorDataRepo
     * @param MonthlySensorDataRepo $monthlySensorDataRepo
     */
    public function __construct(SensorDataRepo $sensorRepo, HourlySensorDataRepo $hourlyRepo, DailySensorDataRepo $dailySensorDataRepo, WeeklySensorDataRepo $weeklySensorDataRepo, MonthlySensorDataRepo $monthlySensorDataRepo)
    {
        $this->sensorRepo = $sensorRepo;
        $this->hourlyRepo = $hourlyRepo;
        $this->dailySensorDataRepo = $dailySensorDataRepo;
        $this->weeklySensorDataRepo = $weeklySensorDataRepo;
        $this->monthlySensorDataRepo = $monthlySensorDataRepo;
    }


    /**
     * @return array
     */
    public function fifteenMinute(): array
    {

            $time = Utility::betweenPreviousFifteenMins(0);
            $startTime = $time['start'];
            $endTime = $time['end'];
            $fifteenMinuteData = $this->sensorRepo->getSensorDataInRange($startTime, $endTime);
            $sensorDataMap = $this->organizeSensorAndWeatherData($fifteenMinuteData);

            $fifteenMinAverages = [];
            foreach ($sensorDataMap as $locationId => $data) {
                $sensorEntries = $data['sensor_data'];
                $weatherEntries = $data['sensor_data'];
                $fifteenMinAverages[$locationId]['sensor_data'] = Utility::calculateSensorAverage($sensorEntries, $locationId);
                $fifteenMinAverages[$locationId]['weather_data'] = Utility::calculateWeatherAverage($weatherEntries, $locationId);
            }

            return $fifteenMinAverages;
    }

    /**
     * @return array
     */
    public function hourly(): array
    {

        $time = Utility::betweenPreviousHours(0);
        $startTime = $time['start'];
        $endTime = $time['end'];
        $data = $this->sensorRepo->getSensorDataInRange($startTime, $endTime);
        $sensorDataMap = $this->organizeSensorAndWeatherData($data);

        $averages = [];
        foreach ($sensorDataMap as $locationId => $data) {
            $sensorEntries = $data['sensor_data'];
            $weatherEntries = $data['sensor_data'];
            $averages[$locationId]['sensor_data'] = Utility::calculateSensorAverage($sensorEntries, $locationId);
            $averages[$locationId]['weather_data'] = Utility::calculateWeatherAverage($weatherEntries, $locationId);
        }

        return $averages;
    }

    /**
     * @param $dailyData
     * @param $date
     * @return array
     */
    public function daily($dailyData = [], $date = null): array
    {

        if(!$dailyData){
            $dailyTime = Utility::betweenPreviousDays(0);
            $startTime = $dailyTime['start'];
            $endTime = $dailyTime['end'];
            $dailyData = $this->hourlyRepo->getHourData($startTime, $endTime);
        }
        $sensorDataMap = $this->organizeSensorAndWeatherData($dailyData);
        $dailyAverages = [];
        foreach ($sensorDataMap as $locationId => $data) {
            $sensorEntries = $data['sensor_data'];
            $weatherEntries = $data['sensor_data'];
            $dailyAverages[$locationId]['sensor_data'] = Utility::calculateSensorAverage($sensorEntries, $locationId,$date);
            $dailyAverages[$locationId]['weather_data'] = Utility::calculateWeatherAverage($weatherEntries, $locationId,$date);
        }

        return $dailyAverages;
    }

    /**
     * @return array
     */
    public function weekly(): array
    {

        $weeklyTime = Utility::betweenPreviousWeeks(0);
        $startTime = $weeklyTime['start'];
        $endTime = $weeklyTime['end'];

        $weeklyData = $this->dailySensorDataRepo->getDailyData($startTime, $endTime);
        $sensorDataMap = $this->organizeSensorAndWeatherData($weeklyData);

        $weeklyAverages = [];
        foreach ($sensorDataMap as $locationId => $data) {
            $sensorEntries = $data['sensor_data'];
            $weatherEntries = $data['sensor_data'];
            $weeklyAverages[$locationId]['sensor_data'] = Utility::calculateSensorAverage($sensorEntries, $locationId);
            $weeklyAverages[$locationId]['weather_data'] = Utility::calculateWeatherAverage($weatherEntries, $locationId);
        }

        return $weeklyAverages;
    }

    /**
     * @param $monthlyData
     * @param $date
     * @return array
     */
    public function monthly($monthlyData = [], $date = null): array
    {


        if(!$monthlyData){
            $monthlyTime = Utility::betweenPreviousMonths(0);
            $startTime = $monthlyTime['start'];
            $endTime = $monthlyTime['end'];
            $monthlyData = $this->weeklySensorDataRepo->getWeeklyData($startTime, $endTime);
        }
        $sensorDataMap = $this->organizeSensorAndWeatherData($monthlyData);

        $monthlyAverages = [];
        foreach ($sensorDataMap as $locationId => $data) {
            $sensorEntries = $data['sensor_data'];
            $weatherEntries = $data['sensor_data'];
            $monthlyAverages[$locationId]['sensor_data'] = Utility::calculateSensorAverage($sensorEntries, $locationId,$date);
            $monthlyAverages[$locationId]['weather_data'] = Utility::calculateWeatherAverage($weatherEntries, $locationId,$date);
        }

        return $monthlyAverages;
    }

    /**
     * @param $annuallyData
     * @param $date
     * @return array
     */
    public function annually($annuallyData = [], $date = null): array
    {

        if(!$annuallyData){
            $annuallyTime = Utility::betweenPreviousYears(0);
            $startTime = $annuallyTime['start'];
            $endTime = $annuallyTime['end'];
            $annuallyData = $this->monthlySensorDataRepo->getMonthlyData($startTime, $endTime);
        }
        $sensorDataMap = $this->organizeSensorAndWeatherData($annuallyData);

        $annuallyAverages = [];
        foreach ($sensorDataMap as $locationId => $data) {
            $sensorEntries = $data['sensor_data'];
            $weatherEntries = $data['sensor_data'];
            $annuallyAverages[$locationId]['sensor_data'] = Utility::calculateSensorAverage($sensorEntries, $locationId,$date);
            $annuallyAverages[$locationId]['weather_data'] = Utility::calculateWeatherAverage($weatherEntries, $locationId,$date);
        }

        return $annuallyAverages;
    }

    /**
     * @param $sensorData
     * @return array
     */
    private function organizeSensorAndWeatherData($sensorData): array
    {
        $sensorDataMap = [];
        foreach ($sensorData as $entry) {
            $sensorLocationId = $entry->sensor_location_id;
            if (!isset($sensorDataMap[$sensorLocationId])) {
                $sensorDataMap[$sensorLocationId] = [
                    'sensor_data' => [],
                ];
            }
            $sensorDataMap[$sensorLocationId]['sensor_data'][] = $entry;
        }
        return $sensorDataMap;
    }
}
