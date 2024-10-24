<?php

namespace App\Repository\User;


use App\ModelsV2\DailyWeatherData;


class DailyWeatherDataRepo
{

    public function saveDailyWeatherList(array $weatherData){
        return DailyWeatherData::insert($weatherData);
    }
}
