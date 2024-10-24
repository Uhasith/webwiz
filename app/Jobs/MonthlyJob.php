<?php

namespace App\Jobs;

use App\ModelsV2\MonthlyWeatherData;
use App\Repository\User\MonthlySensorDataRepo;
use App\Repository\User\MonthlyWeatherDataRepo;
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

class MonthlyJob implements ShouldQueue
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

        $monthlyDataRepo = new MonthlySensorDataRepo();
        $monthlyWeatherRepo = new MonthlyWeatherDataRepo();
        $aqiService = new AqiService();

        $averageFactory = new AverageFactory();


        $monthly =  $averageFactory->getInstance()->monthly();
        if (is_array($monthly)&&!empty($monthly)) {

            foreach ($monthly as $data) {
                $monthlyData = $aqiService->processAirQualityData($data['sensor_data'],Utility::$hour);
                $monthlyWeather = $data['weather_data'];

                $monthlyDataArray[] = $monthlyData;
                $monthlyWeatherDataArray[] = MonthlyWeatherData::formatWeatherData($monthlyWeather,$monthlyData['id']);
            }

            $monthlyDataRepo->saveMonthlySensorDataList($monthlyDataArray);
            $monthlyWeatherRepo->saveMonthlyWeatherList($monthlyWeatherDataArray);

        }



    }
}
