<?php

namespace App\Service\Admin;

use App\Repository\Admin\AnalyticsRepo;
use App\Repository\User\AnuallySensorDataRepo;
use App\Repository\User\AnuallyWeatherDataRepo;
use App\Repository\User\MonthlySensorDataRepo;
use App\Repository\User\MonthlyWeatherDataRepo;
use Carbon\Carbon;

class AnalyticsService
{
    private AnalyticsRepo $analyticsRepo;
    private MonthlySensorDataRepo $monthlySensorDataRepo;
    private AnuallySensorDataRepo $annuallySensorDataRepo;
    private MonthlyWeatherDataRepo $monthlyWeatherDataRepo;
    private AnuallyWeatherDataRepo $annuallyWeatherDataRepo;

    /**
     * @param AnalyticsRepo $analyticsRepo
     * @param MonthlySensorDataRepo $monthlySensorDataRepo
     * @param AnuallySensorDataRepo $annuallySensorDataRepo
     * @param MonthlyWeatherDataRepo $monthlyWeatherDataRepo
     * @param AnuallyWeatherDataRepo $annuallyWeatherDataRepo
     */
    public function __construct(AnalyticsRepo          $analyticsRepo, MonthlySensorDataRepo $monthlySensorDataRepo,
                                AnuallySensorDataRepo  $annuallySensorDataRepo,
                                MonthlyWeatherDataRepo $monthlyWeatherDataRepo, AnuallyWeatherDataRepo $annuallyWeatherDataRepo)
    {
        $this->analyticsRepo = $analyticsRepo;
        $this->monthlySensorDataRepo = $monthlySensorDataRepo;
        $this->annuallySensorDataRepo = $annuallySensorDataRepo;
        $this->monthlyWeatherDataRepo = $monthlyWeatherDataRepo;
        $this->annuallyWeatherDataRepo = $annuallyWeatherDataRepo;
    }

    /**
     * @param $requestData
     * @return mixed
     */
    public function getAqiTrend($requestData): mixed
    {
        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->endOfMonth();
        if ($requestData['period'] == 'Annually') {
            $start = Carbon::now()->subYears(env('ANNUALLY_RANGE'))->startOfYear();
            $end = Carbon::now()->endOfYear();
            return $this->annuallySensorDataRepo->getSensorData($requestData['locationId'], $requestData['equipmentId'], $start, $end);
        }
        return $this->monthlySensorDataRepo->getSensorData($requestData['locationId'], $requestData['equipmentId'], $start, $end);
    }

    /**
     * @param $requestData
     * @return mixed
     */
    public function getWeatherTrend($requestData): mixed
    {
        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->endOfMonth();
        if ($requestData['period'] == 'Annually') {
            $start = Carbon::now()->subYears(env('ANNUALLY_RANGE'))->startOfYear();
            $end = Carbon::now()->endOfYear();
            return $this->annuallyWeatherDataRepo->getWeatherData($requestData['locationId'], $requestData['equipmentId'], $start, $end);
        }
        return $this->monthlyWeatherDataRepo->getWeatherData($requestData['locationId'], $requestData['equipmentId'], $start, $end);

    }

    /**
     * @param $requestData
     * @return array
     */
    public function getTop10Cities($requestData): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        if ($requestData['period'] == 'Annually') {
            $start = Carbon::now()->subYears(env('ANNUALLY_RANGE'))->startOfYear();
            $end = Carbon::now()->endOfYear();
        }
        $result = $this->analyticsRepo->getTop10Cities($requestData['equipmentId'], $requestData['ranking'], $start, $end, $requestData['period']);
        $result = json_decode(json_encode($result), true);
        usort($result, function ($a, $b) {
            return $b['sensor_locations'][0]['sensor_data_Count'] <=> $a['sensor_locations'][0]['sensor_data_Count'];
        });
        return array_slice($result, 0, 10);
    }

    /**
     * @param $requestData
     * @return mixed
     */
    public function getDailyAqiTrendByMonth($requestData)
    {
        $start = Carbon::createFromDate($requestData['year'], $requestData['month'], 1)->startOfMonth();
        $end = Carbon::createFromDate($requestData['year'], $requestData['month'], 1)->endOfMonth();

        return $this->monthlySensorDataRepo->getSensorData($requestData['locationId'], $requestData['equipmentId'], $start, $end);

    }

}
