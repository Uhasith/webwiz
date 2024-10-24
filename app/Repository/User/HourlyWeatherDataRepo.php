<?php

namespace App\Repository\User;

use App\ModelsV2\HourlyWeatherData;

class HourlyWeatherDataRepo
{

    public function saveHourlyWeatherList(array $weatherData){

        return HourlyWeatherData::insert($weatherData);
    }
    public function insert(array $data): void
    {
        $chunks = array_chunk($data, 500);
        foreach ($chunks as $chunk) {
            HourlyWeatherData::insert($chunk);
        }
    }
}
