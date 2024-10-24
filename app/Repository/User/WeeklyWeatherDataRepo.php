<?php

namespace App\Repository\User;


use App\ModelsV2\WeeklyWeatherData;


class WeeklyWeatherDataRepo
{

    public function saveWeeklyWeatherList(array $weatherData){
   
        return WeeklyWeatherData::insert($weatherData);
    }
}
