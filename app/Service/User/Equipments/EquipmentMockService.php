<?php

namespace App\Service\User\Equipments;

use App\Helpers\Utility;
use App\Repository\User\SensorsRepo;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class EquipmentMockService implements IEquipmentService
{

    private SensorsRepo $sensorRepo;

    /**
     * @param SensorsRepo $sensorRepo
     */
    public function __construct(SensorsRepo $sensorRepo)
    {
        $this->sensorRepo = $sensorRepo;
    }


    private function getRandomData($sensorLocationId): array
    {
        $allData[] = [
            "sensor_data" => [
                "sensor_location_id" => $sensorLocationId,
                "PM2.5" => rand(1, 400),
                "PM10" => rand(1, 650),
                "O3" => rand(1, 600),
                "CO" => rand(1, 50050),
                "NO2" => rand(1, 2001),
                "SO2" => rand(1, 1001),
                "CO2" => rand(1, 100),
                "temperature" => rand(-10, 40),
                "humidity" => rand(0, 100),
                "identifier"=> rand(0,100),
                "created_at"=>Carbon::now(),
                "updated_at"=>Carbon::now()
            ],
            "weather_data" => [
                "sensorLocationId" => $sensorLocationId,
                "temperature" => rand(-10, 40),
                "pressure" => rand(950, 1050),
                "humidity" => rand(0, 100),
                "precipitation" => rand(0, 100),
                "cloud" => Utility::$cloudConditions[array_rand(Utility::$cloudConditions)],
                "wind" => rand(0, 100)
            ],
            "optional_data"=>[

            ]
        ];

        return $allData;
    }

    public function ecoTech(): ?array
    {
        $sensor = $this->sensorRepo->getIdBySlug(Utility::$ecoTechSlug);
        if (empty($sensor->sensorLocations)) {
            return null;
        }
        $sensorLocationId = Arr::random($sensor->sensorLocations->pluck('id')->all());
        $sensorData =  $this->getRandomData($sensorLocationId);
        $echoTech = [
            "echoTech"=>[],
            "sensorData"=> $sensorData
        ];
        return $echoTech;
    }

    public function nbro(): ?array
    {
        $sensor = $this->sensorRepo->getIdBySlug(Utility::$nbroSlug);
        if (empty($sensor->sensorLocations)) {
            return null;
        }
        $sensorLocationId = Arr::random($sensor->sensorLocations->pluck('id')->all());
        $nbroData[] = [
            "sensor_data" => [
                "sensor_location_id" => $sensorLocationId,
                "PM1" => rand(1, 100),
                "PM100" => rand(1, 100),
                "PM2.5" => rand(1, 400),
                "PM10" => rand(1, 650),
                "CO" => rand(1, 50050),
                "NO2" => rand(1, 2001),
                "CO2" => rand(1, 100),
                "temperature" => rand(-10, 40),
                "humidity" => rand(0, 100),
            ],
            "weather_data" => [
                "sensorLocationId" => $sensorLocationId,
                "temperature" => rand(-10, 40),
                "pressure" => rand(950, 1050),
                "humidity" => rand(0, 100),
                "cloud" => Utility::$cloudConditions[array_rand(Utility::$cloudConditions)],
                "wind" => rand(0, 100)
            ],
            "optional_data"=>[
               'sensorLocationId' => $sensorLocationId,
               'ch4' => $data['ch4'] ?? null,
               'tvoc' => $data['tvoc'] ?? null,
            ]
        ];
        return $nbroData;

    }

    public function iqAir(): ?array
    {
        $sensor = $this->sensorRepo->getIdBySlug(Utility::$iqAirSlug);

        if (empty($sensor->sensorLocations)) {
            return null;
        }

        $sensorLocationId = Arr::random($sensor->sensorLocations->pluck('id')->all());
        $iqData[] = [
            "sensor_data" => [
                "sensor_location_id" => $sensorLocationId,
                "PM1" => rand(1, 100),
                "PM2.5" => rand(1, 400),
                "PM10" => rand(1, 650),
                "CO2" => rand(1, 100),
                "temperature" => rand(-10, 40),
                "humidity" => rand(0, 100),
            ],
            "weather_data" => [
                "sensorLocationId" => $sensorLocationId,
                "temperature" => rand(-10, 40),
                "pressure" => rand(950, 1050),
                "humidity" => rand(0, 100),
                "cloud" => Utility::$cloudConditions[array_rand(Utility::$cloudConditions)],
                "wind" => rand(0, 100)
            ],
            "optional_data"=>[

            ]
        ];
        return $iqData;
    }

    public function tsiBlueSky(): ?array
    {
        $sensor = $this->sensorRepo->getIdBySlug(Utility::$tsiBlueskySlug);
        if (empty($sensor->sensorLocations)) {
            return null;
        }
        $sensorLocationId = Arr::random($sensor->sensorLocations->pluck('id')->all());
        $tsiData[] = [
            "sensor_data" => [
                "sensor_location_id" => $sensorLocationId,
                "PM1" => rand(1, 100),
                "PM4" => rand(1, 100),
                "PM2.5" => rand(1, 400),
                "PM10" => rand(1, 650),
                "temperature" => rand(-10, 40),
                "humidity" => rand(0, 100),
            ],
            "weather_data" => [
                "sensorLocationId" => $sensorLocationId,
                "temperature" => rand(-10, 40),
                "pressure" => rand(950, 1050),
                "humidity" => rand(0, 100),
                "cloud" => Utility::$cloudConditions[array_rand(Utility::$cloudConditions)],
                "wind" => rand(0, 100)
            ],
            "optional_data"=>[
               "sensor_location_id" => $sensorLocationId,
                "nc_4_0" => rand(1, 100),
                "nc_0_5" => rand(1, 100),
                "nc_1_0" => rand(1, 100),
                "nc_2_5" => rand(1, 100),
                "nc_10" => rand(1, 100),
            ]
        ];
        return $tsiData;
    }
}
