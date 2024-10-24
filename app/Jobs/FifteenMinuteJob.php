<?php

namespace App\Jobs;

use App\AqiService;
use App\Helpers\Utility;
use App\ModelsV2\EquipmentType;
use App\ModelsV2\FifteenMinuteWeatherData;
use App\Repository\User\EquipmentTypeRepo;
use App\Repository\User\FifteenMinuteDataRepo;
use App\Repository\User\FifteenMinuteWeatherRepo;
use App\Repository\User\HourlySensorDataRepo;
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

class FifteenMinuteJob implements ShouldQueue
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
        $fifteenMinuteRepo = new FifteenMinuteDataRepo();
        $fifteenMinuteWeatherRepo = new FifteenMinuteWeatherRepo();
        $aqiService = new AqiService();

        $averageFactory = new AverageFactory();

        $fifteenMinute =  $averageFactory->getInstance()->fifteenMinute();
        if (is_array($fifteenMinute)&&!empty($fifteenMinute)) {
            foreach ($fifteenMinute as $sensorData) {
                $fifteenMinuteData = $aqiService->processAirQualityData($sensorData['sensor_data'],Utility::$minute);
                $fifteenMinuteWeather = $sensorData['weather_data'];

                $sensorDataArray[] = $fifteenMinuteData;
                $weatherDataArray[] = FifteenMinuteWeatherData::formatWeatherData($fifteenMinuteWeather,$fifteenMinuteData['id']);
            }
            $fifteenMinuteRepo->saveFifteenMinuteSensorDataList($sensorDataArray);
            $fifteenMinuteWeatherRepo->saveFifteenMinuteWeatherList($weatherDataArray);

        }

    }
}
