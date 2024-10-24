<?php

namespace App\Jobs;

use App\Repository\User\DailySensorDataRepo;
use App\Repository\User\DailyWeatherDataRepo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\AqiService;
use App\Helpers\Utility;
use App\ModelsV2\DailyWeatherData;
use App\Repository\User\EquipmentTypeRepo;
use App\Repository\User\HourlySensorDataRepo;
use App\Repository\User\SensorDataRepo;
use App\Repository\User\SensorsRepo;
use App\Service\User\AverageCalculation\AverageFactory ;
use App\Service\User\SensorsService;


class DailyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dailyRepo = new DailySensorDataRepo();
        $dailyWeatherRepo = new DailyWeatherDataRepo();
        $aqiService = new AqiService();

        $averageFactory = new AverageFactory();

        $daily =  $averageFactory->getInstance()->daily();
        if (is_array($daily)&&!empty($daily)) {
            foreach ($daily as $sensorData) {
                $dailyData = $aqiService->processAirQualityData($sensorData['sensor_data'],Utility::$hour);
                $dailyWeather = $sensorData['weather_data'];

                $sensorDataArray[] = $dailyData;
                $weatherDataArray[] = DailyWeatherData::formatWeatherData($dailyWeather,$dailyData['id']);
            }
            $dailyRepo->saveDailySensorDataList($sensorDataArray);
            $dailyWeatherRepo->saveDailyWeatherList($weatherDataArray);

        }

    }
}
