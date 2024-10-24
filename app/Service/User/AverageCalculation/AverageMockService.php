<?php

namespace App\Service\User\AverageCalculation;

use App\Helpers\Utility;
use App\Repository\User\SensorLocationRepo;
use Carbon\Carbon;

class AverageMockService implements IAverageService
{

    private SensorLocationRepo $sensorLocationsRepo;

    /**
     * @param SensorLocationRepo $sensorLocationsRepo
     */
    public function __construct(SensorLocationRepo $sensorLocationsRepo)
    {
        $this->sensorLocationsRepo = $sensorLocationsRepo;
    }

    /**
     * @param $sensorLocationId
     * @return array
     */
    private function getRandomData($sensorLocationId): array
    {
        return [
            "sensor_data" => [
                "sensor_location_id" => $sensorLocationId,
                "PM2.5" => rand(1, 100),
                "PM10" => rand(1, 100),
                "O3" => rand(1, 100),
                "CO" => rand(1, 100),
                "NO2" => rand(1, 100),
                "SO2" => rand(1, 100),
                "CO2" => rand(1, 100),
                "temperature" => rand(-10, 40),
                "humidity" => rand(0, 100),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            "weather_data" => [
                "sensorLocationId" => $sensorLocationId,
                "temperature" => rand(-10, 40),
                "pressure" => rand(950, 1050),
                "humidity" => rand(0, 100),
                "precipitation" => rand(0, 100),
                "cloud" => Utility::$cloudConditions[array_rand(Utility::$cloudConditions)],
                "wind" => rand(0, 100)
            ],
            "optional_data" => [
                // Add optional data if necessary
            ]
        ];
    }

    /**
     * @return array
     */
    public function fifteenMinute(): array
    {
        $ids = $this->sensorLocationsRepo->getSensorLocationIds();
        $allData = [];
        foreach ($ids as $sensorLocationId) {
            $allData[] = $this->getRandomData($sensorLocationId);
        }

        return $allData;
    }


    /**
     * @return array
     */
    public function hourly(): array
    {

        $ids = $this->sensorLocationsRepo->getSensorLocationIds();
        $hourlyData = [];
        foreach ($ids as $sensorLocationId) {
            $hourlyData[] = $this->getRandomData($sensorLocationId);
        }

        return $hourlyData;
    }

    /**
     * @param $dailyData
     * @param $date
     * @return array
     */
    public function daily($dailyData = [], $date = null): array
    {

        $ids = $this->sensorLocationsRepo->getSensorLocationIds();
        $dailyData = [];
        foreach ($ids as $sensorLocationId) {
            $dailyData[] = $this->getRandomData($sensorLocationId);
        }

        return $dailyData;
    }

    /**
     * @return array
     */
    public function weekly(): array
    {

        $ids = $this->sensorLocationsRepo->getSensorLocationIds();
        $weeklyData = [];
        foreach ($ids as $sensorLocationId) {
            $weeklyData[] = $this->getRandomData($sensorLocationId);
        }

        return $weeklyData;
    }

    /**
     * @param $monthlyData
     * @param $date
     * @return array
     */
    public function monthly($monthlyData = [], $date = null): array
    {

        $ids = $this->sensorLocationsRepo->getSensorLocationIds();
        $monthlyData = [];
        foreach ($ids as $sensorLocationId) {
            $monthlyData[] = $this->getRandomData($sensorLocationId);
        }

        return $monthlyData;
    }

    /**
     * @param $annuallyData
     * @param $date
     * @return array
     */
    public function annually($annuallyData = [], $date = null): array
    {

        $ids = $this->sensorLocationsRepo->getSensorLocationIds();
        $annuallyData = [];
        foreach ($ids as $sensorLocationId) {
            $annuallyData[] = $this->getRandomData($sensorLocationId);
        }

        return $annuallyData;
    }

}
