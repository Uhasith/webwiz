<?php

namespace App\Jobs;

use App\ModelsV2\WeeklyWeatherData;
use App\Repository\User\WeeklySensorDataRepo;
use App\Repository\User\WeeklyWeatherDataRepo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\AqiService;
use App\Helpers\Utility;
use App\Repository\User\EquipmentTypeRepo;
use App\Repository\User\HourlySensorDataRepo;
use App\Repository\User\SensorDataRepo;
use App\Repository\User\SensorsRepo;
use App\Service\User\AverageCalculation\AverageFactory ;
use App\Service\User\Equipments\EquipmentService;
use App\Service\User\SensorsService;


class WeeklyJob implements ShouldQueue
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

        $weeklyDataRepo = new WeeklySensorDataRepo();
        $weeklyWeatherRepo = new WeeklyWeatherDataRepo();
        $aqiService = new AqiService();

        $averageFactory = new AverageFactory();


        $weekly =  $averageFactory->getInstance()->weekly();
        if (is_array($weekly)&&!empty($weekly)) {

            foreach ($weekly as $data) {
                $weeklyData = $aqiService->processAirQualityData($data['sensor_data'],Utility::$hour);
                $weeklyWeather = $data['weather_data'];

                $weeklyDataArray[] = $weeklyData;
                $weeklyWeatherDataArray[] = WeeklyWeatherData::formatWeatherData($weeklyWeather,$weeklyData['id']);
            }

            $weeklyDataRepo->saveWeeklySensorDataList($weeklyDataArray);
            $weeklyWeatherRepo->saveWeeklyWeatherList($weeklyWeatherDataArray);

        }



    }
}
