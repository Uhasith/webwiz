<?php

namespace App\Jobs;

use App\AqiService;
use App\Helpers\Utility;
use App\ModelsV2\WeatherRecords;
use App\Repository\User\EchoTechSensorDataRepo;
use App\Repository\User\EquipmentTypeRepo;
use App\Repository\User\HourlySensorDataRepo;
use App\Repository\User\OptionalSensorDataRepo;
use App\Repository\User\SensorDataRepo;
use App\Repository\User\SensorLocationRepo;
use App\Repository\User\SensorsRepo;
use App\Repository\User\WeatherRecordsRepo;
use App\Service\User\Equipments\EquipmentFactory;
use App\Service\User\SensorsService;
use App\Service\User\WeatherService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Webpatser\Uuid\Uuid;

class SensorsJob implements ShouldQueue
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
        Log::info("Sensor data scheduler has started");

        $sensorRepo = new SensorsRepo();
        $sensorDataRepo = new SensorDataRepo();
        $aqiService = new AqiService();
        $equipmentType = new EquipmentTypeRepo();
        $weatherRepo = new WeatherRecordsRepo();
        $optionalDataRepo = new OptionalSensorDataRepo();
        $sensorLocation = new SensorLocationRepo();
        $hourly = new HourlySensorDataRepo();
        $echoTechRepo = new EchoTechSensorDataRepo();

        $sensorService = new SensorsService($sensorRepo, $sensorDataRepo,$hourly, $equipmentType,$aqiService);
        $weatherService = new WeatherService($weatherRepo, $sensorLocation);

        $equipmentFactory =  new EquipmentFactory();

        $iqAir =  $equipmentFactory->getInstance()->iqAir();
        if (is_array($iqAir)) {
            foreach ($iqAir as $sensorData) {
                $sensorDataIqAir = $aqiService->processAirQualityData($sensorData['sensor_data'],null);
                $weatherDataIqAir = $sensorData['weather_data'];

                $sensorDataArray[] = $sensorDataIqAir;
                $weatherDataArray[] = WeatherRecords::formatWeatherData($weatherDataIqAir,$sensorDataIqAir['id']);
            }
            $sensorService->saveSensorDataList($sensorDataArray ?? []);
            $weatherService->saveWeatherList($weatherDataArray ?? []);

        }

        $nbro =  $equipmentFactory->getInstance()->nbro();
        if (is_array($nbro)) {
            foreach ($nbro as $sensorData) {
                $sensorDataNbro = $aqiService->processAirQualityData($sensorData['sensor_data'],null);
                $weatherDataNbro = $sensorData['weather_data'];
                $optionalDataNbro = $sensorData['optional_data'];

                $sensorNbroDataArray[] = $sensorDataNbro;
                $weatherNbroDataArray[] = WeatherRecords::formatWeatherData($weatherDataNbro,$sensorDataNbro['id']);
                $optionalNbroDataArray[] = Utility::formatOptionalData($optionalDataNbro,$sensorDataNbro['id']);
            }

            $sensorService->saveSensorDataList($sensorNbroDataArray ?? []);
            $weatherService->saveWeatherList($weatherNbroDataArray ?? []);
            $optionalDataRepo->insert($optionalNbroDataArray ?? []);

        }

        $tsiBlueSky =  $equipmentFactory->getInstance()->tsiBlueSky();
        if (is_array($tsiBlueSky)) {
            foreach ($tsiBlueSky as $sensorData) {
                $sensorDataTsiBlueSky = $aqiService->processAirQualityData($sensorData['sensor_data'],null);
                $weatherDataTsiBlueSky = $sensorData['weather_data'];
                $optionalDataTsiBlueSky = $sensorData['optional_data'];

                $sensorDataTsiBlueSkyArray[] = $sensorDataTsiBlueSky;
                $weatherTsiBlueSkyDataArray[] = WeatherRecords::formatWeatherData($weatherDataTsiBlueSky,$sensorDataTsiBlueSky['id']);
                $optionalTsiBlueSkyDataArray[] = Utility::formatOptionalData($optionalDataTsiBlueSky,$sensorDataTsiBlueSky['id']);
            }

            $sensorService->saveSensorDataList($sensorDataTsiBlueSkyArray ?? []);
            $weatherService->saveWeatherList($weatherTsiBlueSkyDataArray ?? []);
            $optionalDataRepo->insert($optionalTsiBlueSkyDataArray ?? []);


        }

        $ecoTech = $equipmentFactory->getInstance()->ecoTech();
        if (is_array($ecoTech)) {
            $sensorDataEcoTechArray = [];
            $weatherEcoTechDataArray = [];
            $optionalEcoTechDataArray = [];
            $data = $ecoTech['sensorData'];

            foreach ($data as $sensorData) {

                if (!($sensorDataRepo->checkSensorDataDuplicate($sensorData['sensor_data']['identifier']))) {
                    $sensorDataEcoTech = $aqiService->processAirQualityData($sensorData['sensor_data'],Utility::$history);
                    $weatherDataEcoTech = $sensorData['weather_data'];
                    $optionalDataEcoTech = $sensorData['optional_data'];

                    $sensorDataEcoTechArray[] = $sensorDataEcoTech;
                    $weatherEcoTechDataArray[] = WeatherRecords::formatWeatherData($weatherDataEcoTech, $sensorDataEcoTech['id']);
                    $optionalEcoTechDataArray[] = Utility::formatOptionalData($optionalDataEcoTech, $sensorDataEcoTech['id']);
                }

            }

            if (!empty($sensorDataEcoTechArray)) {
                $sensorService->saveSensorDataList($sensorDataEcoTechArray);
            }

            if (!empty($weatherEcoTechDataArray)) {
                $weatherService->saveWeatherList($weatherEcoTechDataArray);
            }

            if (!empty($optionalEcoTechDataArray)) {
                $optionalDataRepo->insert($optionalEcoTechDataArray);
            }

            $echoTechRepo->saveEchoTechDataList($ecoTech['echoTech']);
        }


        Log::info("Sensor data scheduler is completed");
    }

}
