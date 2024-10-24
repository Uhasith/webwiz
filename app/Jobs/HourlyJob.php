<?php

namespace App\Jobs;

use App\AqiService;
use App\Helpers\Utility;
use App\ModelsV2\EquipmentType;
use App\ModelsV2\FifteenMinuteWeatherData;
use App\ModelsV2\HourlyWeatherData;
use App\Repository\User\EquipmentTypeRepo;
use App\Repository\User\FifteenMinuteDataRepo;
use App\Repository\User\FifteenMinuteWeatherRepo;
use App\Repository\User\HourlySensorDataRepo;
use App\Repository\User\HourlyWeatherDataRepo;
use App\Repository\User\SensorDataRepo;
use App\Repository\User\SensorsRepo;
use App\Service\User\AverageCalculation\AverageFactory ;
use App\Service\User\Equipments\EquipmentService;
use App\Service\User\SensorsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HourlyJob implements ShouldQueue
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
        $hourlyDataRepo = new HourlySensorDataRepo();
        $hourlyWeatherRepo = new HourlyWeatherDataRepo();
        $aqiService = new AqiService();
        $hourly = new HourlySensorDataRepo();

        $averageFactory = new AverageFactory();


        $hourly =  $averageFactory->getInstance()->hourly();
        if (is_array($hourly)&&!empty($hourly)) {

            foreach ($hourly as $data) {
                $hourlyData = $aqiService->processAirQualityData($data['sensor_data'],Utility::$hour);
                $hourlyWeather = $data['weather_data'];

                $hourlyDataArray[] = $hourlyData;
                $hourlyWeatherDataArray[] = HourlyWeatherData::formatHourlyWeatherData($hourlyWeather,$hourlyData['id']);
            }

            $hourlyDataRepo->saveHourlySensorDataList($hourlyDataArray);
            $hourlyWeatherRepo->saveHourlyWeatherList($hourlyWeatherDataArray);

        }



    }
}
