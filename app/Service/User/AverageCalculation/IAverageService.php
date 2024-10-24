<?php

namespace App\Service\User\AverageCalculation;

interface IAverageService
{
    /**
     * @return mixed
     */
    public function fifteenMinute(): mixed;

    /**
     * @return mixed
     */
    public function hourly(): mixed;

    /**
     * @param $dailyData
     * @param $date
     * @return mixed
     */
    public function daily($dailyData, $date): mixed;

    /**
     * @return mixed
     */
    public function weekly(): mixed;

    /**
     * @param $monthlyData
     * @param $date
     * @return mixed
     */
    public function monthly($monthlyData, $date): mixed;

    /**
     * @param $annuallyData
     * @param $date
     * @return mixed
     */
    public function annually($annuallyData, $date): mixed;

}
