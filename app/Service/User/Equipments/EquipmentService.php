<?php

namespace App\Service\User\Equipments;


use App\Helpers\ExternalApis;
use App\Helpers\Utility;
use App\Imports\Extract\SplitTextExport;
use App\Imports\Extract\WQDExtract;
use App\ModelsV2\EchoTechSensorData;
use App\Repository\User\EchoTechSensorDataRepo;
use App\Repository\User\SensorsRepo;
use App\Service\User\WeatherService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EquipmentService implements IEquipmentService
{
    private SensorsRepo $sensorRepo;
    private WeatherService $weatherService;

    public function __construct(SensorsRepo $sensorRepo, WeatherService $weatherService)
    {
        $this->sensorRepo = $sensorRepo;
        $this->weatherService = $weatherService;

    }

    /**
     * @return array
     */
    public function ecoTech(): array
    {
        $sensors = $this->sensorRepo->getIdBySlug(Utility::$ecoTechSlug);

        $allData = [];
        $echoTechFormattedData = [];

        foreach ($sensors->sensorLocations as $sensorLocation) {
            $sensorLocationId = $sensorLocation->id;
            $locationName = $sensorLocation->location->name;

            $ecoTechData = new SplitTextExport($locationName);
            $datas = json_decode($ecoTechData->collection(), true);
            $data = end($datas['data']);
            if($datas['data'] && $data){

                $dateTime = Utility::formatDateTimeEchoTech($data['created_at']);
                $allData[] = [
                    'sensor_data' => [
                        'sensor_location_id' => $sensorLocationId,
                        'PM2.5' => $data['PM2.5 Conc'] ?? 0,
                        'PM10' => $data['PM10 Conc'] ?? 0,
                        'PM1' => $data['pm1'] ?? 0,
                        'PM100' => $data['pm100'] ?? 0,
                        'O3' => $data['O3 Conc'] ?? 0,
                        'CO' => $data['CO Conc'] ?? 0,
                        'NO2' => $data['NO2 Conc'] ?? 0,
                        'SO2' => $data['SO2 Conc'] ?? 0,
                        'CO2' => $data['CO2 Conc'] ?? 0,
                        'temperature' => $data['tp'] ?? null,
                        'humidity' => $data['hm'] ?? null,
                        'identifier' => $data['key'],
                        'created_at' => $dateTime,
                        'updated_at' => $dateTime
                    ],
                    'weather_data' => [
                        'sensor_location_id' => $sensorLocationId,
                        'temperature' => $data['tp'] ?? null,
                        'pressure' => $weatherData['pressure'] ?? null,
                        'humidity' => $data['hm'] ?? null,
                        'cloud' => $weatherData['cloud'] ?? null,
                        'wind' => $weatherData['wind'] ?? null,
                        'created_at' => $dateTime,
                        'updated_at' => $dateTime
                    ],
                    'optional_data' => [
                        'sensor_location_id' => $sensorLocationId,
                        'at' => $data['AT'] ?? null,
                        'rh' => $data['RH'] ?? null,
                        'bp' => $data['BP'] ?? null,
                        'sigma' => $data['Sigma'] ?? null,
                        'ws_raw' => $data['WS Raw'] ?? null,
                        'wd_raw' => $data['WD Raw'] ?? null,
                        'ws_average' => $data['WS Average'] ?? null,
                        'wd_average' => $data['WD Average'] ?? null,
                        //not sure
                        'pm2_5_qtotal' => $data['PM2.5 Qtot'] ?? null,
                        'pm2_5_rh' => $data['PM2.5 RH'] ?? null,
                        'pm2_5_at' => $data['PM2.5 AT'] ?? null,
                        'pm2_5_maint' => $data['PM2.5 Maint'] ?? null,
                        'pm2_5_pwr_fail' => $data['PM2.5 Pwr Fail'] ?? null,
                        'pm2_5_ref_memb' => $data['PM2.5 Ref Memb'] ?? null,
                        'pm2_5_nz_alarm' => $data['PM2.5 Nz Alarm'] ?? null,
                        'pm2_5_flow_alarm' => $data['PM2.5 Flow Alarm'] ?? null,
                        'pm2_5_press_alarm' => $data['PM2.5 Press Alarm'] ?? null,
                        //not sure
                        'pm2_5_span_chk' => $data['PM2.5 Spn Chk Error'] ?? null,
                        'pm2_5_cnt_alarm' => $data['PM2.5 Cnt Alarm'] ?? null,
                        'pm2_5_tape_alarm' => $data['PM2.5 Tape Alarm'] ?? null,
                        'pm10_qtotal' => $data['PM10 Qtot'] ?? null,
                        'pm10_rh' => $data['PM10 RH'] ?? null,
                        'pm10_at' => $data['PM10 AT'] ?? null,
                        'pm10_maint' => $data['PM10 Maint'] ?? null,
                        'pm10_pwr_fail' => $data['PM10 Pwr Fail'] ?? null,
                        'pm10_ref_memb' => $data['PM10 Ref Memb'] ?? null,
                        'pm10_nz_alarm' => $data['PM10 Nz Alarm'] ?? null,
                        'pm10_flow_alarm' => $data['PM10 Flow Alarm'] ?? null,
                        'pm10_press_alarm' => $data['PM10 Press Alarm'] ?? null,
                        'pm10_span_chk' => $data['PM10 Span Chk'] ?? null,
                        'pm10_cnt_alarm' => $data['PM10 Cnt Alarm'] ?? null,
                        'pm10_tape_alarm' => $data['PM10 Tape Alarm'] ?? null,
                        'o3_gas_press' => $data['O3 Gas Press'] ?? null,
                        'co_amb_press' => $data['CO Amb Press'] ?? null,
                        'nox' => $data['NOX Conc'] ?? null,
                        'nox_gas_press' => $data['NOX Gas Press'] ?? null,
                        'no' => $data['NO Conc'] ?? null,

                        'voc_sample_flow' => $data['VOC  Sample '] ?? null,
                        'voc_fuel_flow' => $data['VOC Fuel '] ?? null,
                        'voc_oxid_temp' => $data['VOC Oxid '] ?? null,
                        'created_at' => $dateTime,
                        'updated_at' => $dateTime

                    ]
                ];

                foreach ($datas['data'] as $echoTechData) {
                    $formattedData = EchoTechSensorData::formatEchoTechData($echoTechData);
                    $echoTechFormattedData[] = $formattedData;

                }
            }

        }

        return [
            'echoTech' => $echoTechFormattedData,
            'sensorData' => $allData,
        ];

    }

    /**
     * @return array
     */
    public function nbro(): array
    {
        $sensor = $this->sensorRepo->getIdBySlug(Utility::$iqAirSlug);
        $allData = [];
        foreach ($sensor->sensorLocations as $location) {
            $index = $location->locationindex;
            $sensorLocationId = $location->id;
            // $nbroData = Utility::sendApiRequest(
            //     ExternalApis::iqAirApi,
            //     "v2/{$index}"
            // );

            // $weatherData = $this->weatherService->getWeatherStats($sensorLocationId);
            // Log::info($weatherData);
            $data = [];
            $weatherData = [];

            $allData[] = [
                'sensor_data' => [
                    'sensor_location_id' => $sensorLocationId,
                    'PM2.5' => $data['pm25'] ?? 0,
                    'PM10' => $data['pm10'] ?? 0,
                    'PM1' => $data['pm1'] ?? 0,
                    'PM100' => $data['pm100'] ?? 0,
                    'CO' => $data['co'] ?? 0,
                    'NO2' => $data['no2'] ?? 0,
                    'CO2' => $data['co2'] ?? 0,
                    'temperature' => $data['tp'] ?? null,
                    'humidity' => $data['hm'] ?? null,
                    'created_at' => $data['created_at'] ?? Carbon::now(),
                    'updated_at' => $data['updated_at'] ?? Carbon::now()
                ],
                'weather_data' => [
                    'sensor_location_id' => $sensorLocationId,
                    'temperature' => $data['tp'] ?? null,
                    'pressure' => $weatherData['pressure'] ?? null,
                    'humidity' => $data['hm'] ?? null,
                    'cloud' => $weatherData['cloud'] ?? null,
                    'wind' => $weatherData['wind'] ?? null,
                    'created_at' => $data['created_at'] ?? Carbon::now(),
                    'updated_at' => $data['updated_at'] ?? Carbon::now()
                ],
                'optional_data' => [
                    'sensor_location_id' => $sensorLocationId,
                    'ch4' => $data['ch4'] ?? null,
                    'tvoc' => $data['tvoc'] ?? null,
                ]
            ];
            Log::info($allData);
        }
        return $allData;
    }

    /**
     * @return array
     */
    public function iqAir(): array
    {
        $sensor = $this->sensorRepo->getIdBySlug(Utility::$iqAirSlug);
        $allData = [];
        foreach ($sensor->sensorLocations as $location) {
            $index = $location->locationindex;
            $sensorLocationId = $location->id;
            $iqData = Utility::sendApiRequest(
                ExternalApis::iqAirApi,
                "v2/{$index}"
            );

            $weatherData = $this->weatherService->getWeatherStats($sensorLocationId);
            $data = json_decode($iqData, true);

            $allData[] = [
                'sensor_data' => [
                    'sensor_location_id' => $sensorLocationId,
                    'PM2.5' => $data['historical']['instant'][0]['pm25']['conc'] ?? 0,
                    'PM10' => $data['historical']['instant'][0]['pm10']['conc'] ?? 0,
                    'PM1' => $data['historical']['instant'][0]['pm1'] ?? 0,
                    'CO2' => $data['historical']['instant'][0]['co2'] ?? 0,
                    'temperature' => $data['historical']['instant'][0]['tp'] ?? null,
                    'humidity' => $data['historical']['instant'][0]['hm'] ?? null,
                    'created_at' => $data['created_at'] ?? Carbon::now(),
                    'updated_at' => $data['updated_at'] ?? Carbon::now()
                ],
                'weather_data' => [
                    'sensor_location_id' => $sensorLocationId,
                    'temperature' => $data['historical']['instant'][0]['tp'] ?? null,
                    'pressure' => $data['historical']['instant'][0]['pr'] ?? null,
                    'humidity' => $data['historical']['instant'][0]['hm'] ?? null,
                    'cloud' => $weatherData['cloud'] ?? null,
                    'wind' => $weatherData['wind'] ?? null,
                    'created_at' => $data['created_at'] ?? Carbon::now(),
                    'updated_at' => $data['updated_at'] ?? Carbon::now()
                ]
            ];
            Log::info($allData);
        }
        return $allData;
    }

    /**
     * @return array
     */
    public function tsiBlueSky(): array
    {
        $sensor = $this->sensorRepo->getIdBySlug(Utility::$tsiBlueskySlug);
        $allData = [];
        foreach ($sensor->sensorLocations as $location) {
            $index = $location->locationindex;
            $sensorLocationId = $location->id;
            // $nbroData = Utility::sendApiRequest(
            //     ExternalApis::iqAirApi,
            //     "v2/{$index}"
            // );

            // $weatherData = $this->weatherService->getWeatherStats($sensorLocationId);
            // Log::info($weatherData);
            // $data = json_decode($tsiData, true);

            $data = [];
            $weatherData = [];


            $allData[] = [
                'sensor_data' => [
                    'sensor_location_id' => $sensorLocationId,
                ],
                'weather_data' => [
                    'sensor_location_id' => $sensorLocationId,
                    'pressure' => $weatherData['pressure'] ?? null,
                    'cloud' => $weatherData['cloud'] ?? null,
                ],
                'optional_data' => [
                    'sensor_location_id' => $sensorLocationId,
                ]
            ];

            if($data){
                foreach ($data[0]['sensors'] as $sensor) {
                    if ($sensor['type'] === 'pm') {
                        foreach ($sensor['measurements'] as $measurement) {
                            $name = $measurement['name'];
                            $type = $measurement['type'];
                            $value = $measurement['data']['value'];

                            if ($name === 'PM 2.5' || $type === 'mcpm2x5') {
                                $allData[0]['sensor_data']['PM2.5'] = $value;
                            }
                            if ($name === 'PM 10' || $type === 'mcpm10') {
                                $allData[0]['sensor_data']['PM10'] = $value;
                            }
                            if ($name === 'PM 1.0' || $type === 'mcpm1x0') {
                                $allData[0]['sensor_data']['PM1'] = $value;
                            }
                            if ($name === 'PM 4.0' || $type === 'mcpm4x0') {
                                $allData[0]['sensor_data']['PM4'] = $value;
                            }
                            if ($name === 'Fan Speed' || $type === 'fanspd') {
                                $allData[0]['weather_data']['wind'] = $value; // TODO:: (rpm) need to convert into km/h
                            }
                            if ($name === 'NC 4.0' || $type === 'ncpm4x0') {
                                $allData[0]['optional_data']['nc_4_0'] = $value;
                            }
                            if ($name === 'NC 0.5' || $type === 'ncpm0x5') {
                                $allData[0]['optional_data']['nc_0_5'] = $value;
                            }
                            if ($name === 'NC 1.0' || $type === 'nc1x0') {
                                $allData[0]['optional_data']['nc_1_0'] = $value;
                            }
                            if ($name === 'NC 2.5' || $type === 'ncpm2x5') {
                                $allData[0]['optional_data']['nc_2_5'] = $value;
                            }
                            if ($name === 'NC 10' || $type === 'ncpm10') {
                                $allData[0]['optional_data']['nc_10'] = $value;
                            }
                        }
                    }
                    if ($sensor['type'] === 'temp_rh') {
                        foreach ($sensor['measurements'] as $measurement2) {
                            $name = $measurement2['name'];
                            $type = $measurement2['type'];
                            $value = $measurement2['data']['value'];

                            if ($name === 'Temperature' || $type === 'temp_c') {
                                $allData[0]['weather_data']['temperature'] = $value;
                                $allData[0]['sensor_data']['temperature'] = $value;
                            }
                            if ($name === 'Relative Humidity' || $type === 'rh_percent') {
                                $allData[0]['weather_data']['humidity'] = $value;
                                $allData[0]['sensor_data']['humidity'] = $value;
                            }
                        }
                    }

                }

                Log::info($allData);
                return $allData;
            }

        }
        return [];
    }

}
