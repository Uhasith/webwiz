<?php

namespace App\Jobs;

use App\ModelsV2\AnuallyWeatherData;
use App\Repository\User\AnuallySensorDataRepo;
use App\Repository\User\AnuallyWeatherDataRepo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\AqiService;
use App\Helpers\Utility;
use App\Service\User\AverageCalculation\AverageFactory ;

class AnnuallyJob implements ShouldQueue
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

        $anuallyDataRepo = new AnuallySensorDataRepo();
        $anuallyWeatherRepo = new AnuallyWeatherDataRepo();
        $aqiService = new AqiService();

        $averageFactory = new AverageFactory();


        $anually =  $averageFactory->getInstance()->annually();
        if (is_array($anually)&&!empty($anually)) {

            foreach ($anually as $data) {
                $anuallyData = $aqiService->processAirQualityData($data['sensor_data'],Utility::$hour);
                $anuallyWeather = $data['weather_data'];

                $anuallyDataArray[] = $anuallyData;
                $anuallyWeatherDataArray[] = AnuallyWeatherData::formatWeatherData($anuallyWeather,$anuallyData['id']);
            }

            $anuallyDataRepo->saveAnuallySensorDataList($anuallyDataArray);
            $anuallyWeatherRepo->saveAnuallyWeatherList($anuallyWeatherDataArray);

        }

    }
}
