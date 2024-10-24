<?php

namespace App\Service\User;

use App\Helpers\ExternalApis;
use App\Helpers\Utility;
use App\ModelsV2\WeatherRecords;
use App\Repository\User\SensorLocationRepo;
use App\Repository\User\WeatherRecordsRepo;
use Illuminate\Support\Facades\Log;

class WeatherService{

    private WeatherRecordsRepo $weatherRecordsRepo;
    private SensorLocationRepo $sensorLocationRepo;


    /**
     * @param WeatherRecordsRepo $weatherRecordsRepo
     * @param SensorLocationRepo $sensorLocationRepo
     */
    public function __construct(WeatherRecordsRepo $weatherRecordsRepo, SensorLocationRepo $sensorLocationRepo)
    {
        $this->weatherRecordsRepo = $weatherRecordsRepo;
        $this->sensorLocationRepo = $sensorLocationRepo;
    }

    public function getWeatherStats($sensorLocationId): array
    {

        $sensorLocation=  $this->sensorLocationRepo->getById($sensorLocationId);
        $lat = $sensorLocation->latitude;
        $long = $sensorLocation->longitude;
        $apiKey = env('WEATHER_API_KEY');

        $weatherStats = Utility::sendApiRequest(
            ExternalApis::weatherApi,
            'v3/aggcommon/v3alertsHeadlines;v3-wx-observations-current;v3-location-point',
            [
                'apiKey' => $apiKey,
                'geocodes' => $lat . "," . $long,
                'language' => 'en-US',
                'units' => 'm',
                'format' => 'json'
            ]
        );


        $data = json_decode($weatherStats, true);
        Log::info($data);

        return [
            'sensorLocationId'=>$sensorLocationId,
            'temperature' => $data[0]['v3-wx-observations-current']['temperature'],
            'wind' => $data[0]['v3-wx-observations-current']['windSpeed'],
            'humidity' => $data[0]['v3-wx-observations-current']['relativeHumidity'],
            'pressure' => $data[0]['v3-wx-observations-current']['pressureAltimeter'],
            'cloud' => $data[0]['v3-wx-observations-current']['cloudCoverPhrase']
        ];

    }

    public function saveTempWeather($data,$sensorDataId): WeatherRecords
    {
        return $this->weatherRecordsRepo->save($data,$sensorDataId);
    }

    public  function saveWeatherList($weatherData){
        return $this->weatherRecordsRepo->saveWeatherList($weatherData);
    }

}
