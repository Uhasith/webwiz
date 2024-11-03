<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

class Utility
{
    public static string $statusActive = "ACTIVE";
    public static string $statusInactive = "INACTIVE";
    public static string $statusPending = "PENDING";
    public static string $nbroSlug = "nbro";
    public static string $tsiBlueskySlug = "tsi_bluesky";
    public static string $ecoTechSlug = "eco_tech";
    public static string $iqAirSlug = "iq_air";
    public static string $superAdmin = "superadmin";
    public static string $admin = "admin";
    public static string $user = "user";
    public static string $minute = "minute";
    public static string $hour = "hour";
    public static string $daily = "daily";
    public static string $weekly = "weekly";
    public static string $monthly = "monthly";
    public static string $annually = "annually";
    public static string $history = "history";
    public static string $typeSystem = "SYSTEM";
    public static string $typeManual = "MANUAL";
    public static array $cloudConditions = ['cloudy', 'sunny', 'rainy', 'partly cloudy'];

    public static array $ecoTechStatus = [128, 64, 0];
    public static int $ecoTechExcelColumnCount = 18;
    public static int $iqAirExcelColumnCount = 58;
    public static string $sriLankaTimeZone = 'Asia/Colombo';


    /**
     * @param $baseUrl
     * @param $endpoint
     * @param array $params
     * @return Response
     */
    public static function sendApiRequest($baseUrl, $endpoint, array $params = []): \Illuminate\Http\Client\Response
    {
        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

        return Http::get($url, $params);
    }

    /**
     * @param $data
     * @return null[]|string[]
     */
    public static function validateAirQualityData($data): array
    {
        $pm2_5_status = isset($data["PM2.5"]) ? self::$statusActive : null;
        $pm10_status = isset($data["PM10"]) ? self::$statusActive : null;
        $co_status = isset($data["CO"]) ? self::$statusActive : null;
        $o3_status = isset($data["O3"]) ? self::$statusActive : null;
        $so2_status = isset($data["SO2"]) ? self::$statusActive : null;
        $no2_status = isset($data["NO2"]) ? self::$statusActive : null;
        $temp_status = isset($data["temperature"]) ? self::$statusActive : null;
        $humidity_status = isset($data["humidity"]) ? self::$statusActive : null;


        if ($data["PM2.5"] && $data["PM10"]) {
            $pm2_5_status && $pm10_status = $data["PM10"] >= $data["PM2.5"] ? self::$statusActive : self::$statusPending;
        }
        if (!$data["PM2.5"]) {
            $pm2_5_status = self::$statusPending;
        }
        if (!$data["PM10"]) {
            $pm10_status = self::$statusPending;
        }

        if (isset($data["temperature"])) {
            $temp_status = ($data["temperature"] < 50 && $data["temperature"] > 0) ? self::$statusActive : self::$statusPending;
        }

        if (isset($data["humidity"])) {
            $humidity_status = ($data["humidity"] < 100 && $data["humidity"] > 1) ? self::$statusActive : self::$statusPending;
        }

        if (isset($data["PM2.5"])) {
            $pm2_5_status = ($data["PM2.5"] <= 0 || $data["PM2.5"] > 400.37) ? self::$statusPending : $pm2_5_status;
        }

        if (isset($data["PM10"])) {
            $pm10_status = ($data["PM10"] <= 0 || $data["PM10"] > 650.49) ? self::$statusPending : $pm10_status;
        }

        if (isset($data["NO2"])) {
            $no2_status = ($data["NO2"] <= 0 || $data["NO2"] > 2001.87) ? self::$statusPending : $no2_status;
        }

        if (isset($data["SO2"])) {
            $so2_status = ($data["SO2"] <= 0 || $data["SO2"] > 1000.99) ? self::$statusPending : $so2_status;
        }

        if (isset($data["O3"])) {
            $o3_status = ($data["O3"] <= 0 || $data["O3"] > 600.49) ? self::$statusPending : $o3_status;
        }

        if (isset($data["CO"])) {
            $co_status = ($data["CO"] <= 0 || $data["CO"] > 50049) ? self::$statusPending : $co_status;
        }


        return [
            "PM2.5" => $pm2_5_status,
            "PM10" => $pm10_status,
            "CO" => $co_status,
            "O3" => $o3_status,
            "SO2" => $so2_status,
            "NO2" => $no2_status,
            "temperature" => $temp_status,
            "humidity" => $humidity_status,
        ];
    }

    /**
     * @param $data
     * @param $locationId
     * @param $date
     * @return array
     */
    public static function calculateSensorAverage($data, $locationId, $date = null): array
    {

        $sumPm25 = $sumPm10 = $sumO3 = $sumCo = $sumNo2 = $sumSo2 = $sumCo2 = $sumPm100 = $sumPm1 = $sumPm4 = 0;
        $countPm25 = $countPm10 = $countO3 = $countCo = $countNo2 = $countSo2 = $countCo2 = $countPm100 = $countPm1 = $countPm4 = 0;

        $identifier = null;
        foreach ($data as $entry) {
            $identifier = $entry->identifier ?? null;
            if (!is_null($entry->pm2_5)) {
                $sumPm25 += $entry->pm2_5;
                $countPm25++;
            }

            if (!is_null($entry->pm10)) {
                $sumPm10 += $entry->pm10;
                $countPm10++;
            }

            if (!is_null($entry->o3)) {
                $sumO3 += $entry->o3;
                $countO3++;
            }

            if (!is_null($entry->co)) {
                $sumCo += $entry->co;
                $countCo++;
            }

            if (!is_null($entry->no2)) {
                $sumNo2 += $entry->no2;
                $countNo2++;
            }

            if (!is_null($entry->so2)) {
                $sumSo2 += $entry->so2;
                $countSo2++;
            }

            if (!is_null($entry->co2)) {
                $sumCo2 += $entry->co2;
                $countCo2++;
            }

            if (!is_null($entry->pm100)) {
                $sumPm100 += $entry->pm100;
                $countPm100++;
            }

            if (!is_null($entry->pm1)) {
                $sumPm1 += $entry->pm1;
                $countPm1++;
            }

            if (!is_null($entry->pm4)) {
                $sumPm4 += $entry->pm4;
                $countPm4++;
            }
        }

        return [
            "sensor_location_id" => $locationId,
            'PM2.5' => $countPm25 > 0 ? $sumPm25 / $countPm25 : 0,
            'PM10' => $countPm10 > 0 ? $sumPm10 / $countPm10 : 0,
            'O3' => $countO3 > 0 ? $sumO3 / $countO3 : 0,
            'CO' => $countCo > 0 ? $sumCo / $countCo : 0,
            'NO2' => $countNo2 > 0 ? $sumNo2 / $countNo2 : 0,
            'SO2' => $countSo2 > 0 ? $sumSo2 / $countSo2 : 0,
            'CO2' => $countCo2 > 0 ? $sumCo2 / $countCo2 : 0,
            'PM1' => $countPm1 > 0 ? $sumPm1 / $countPm1 : 0,
            'PM100' => $countPm100 > 0 ? $sumPm100 / $countPm100 : 0,
            'PM4' => $countPm4 > 0 ? $sumPm4 / $countPm4 : 0,
            'identifier' => $identifier,
            'created_at' => $date,
            'updated_at' => Carbon::now()
        ];
    }

    /**
     * @param $weatherData
     * @param $locationId
     * @param $date
     * @return array
     */
    public static function calculateWeatherAverage($weatherData, $locationId, $date= null): array
    {
        $sumTemperature = $sumHumidity = $sumWindSpeed = $sumPrecipitation = $sumPressure = 0;
        $countTemperature = $countHumidity = $countWindSpeed = $countPrecipitation = $countPressure = 0;

        foreach ($weatherData as $entry) {
            $weatherRecord = $entry->weatherRecords;
            if($weatherRecord){
                if (!is_null($weatherRecord->humidity)) {
                    $sumHumidity += $weatherRecord->humidity;
                    $countHumidity++;
                }

                if (!is_null($weatherRecord->wind)) {
                    $sumWindSpeed += $weatherRecord->wind;
                    $countWindSpeed++;
                }

                if (!is_null($weatherRecord->pressure)) {
                    $sumPressure += $weatherRecord->pressure;
                    $countPressure++;
                }

                if (!is_null($weatherRecord->temperature)) {
                    $sumTemperature += $weatherRecord->temperature;
                    $countTemperature++;
                }

                if (!is_null($weatherRecord->precipitation)) {
                    $sumPrecipitation += $weatherRecord->precipitation;
                    $countPrecipitation++;
                }
            }
        }

        return [
            "sensor_location_id" => $locationId,
            'temperature' => $countTemperature > 0 ? $sumTemperature / $countTemperature : 0,
            'pressure' => $countPressure > 0 ? $sumPressure / $countPressure : 0,
            'humidity' => $countHumidity > 0 ? $sumHumidity / $countHumidity : 0,
            'precipitation' => $countPrecipitation > 0 ? $sumPrecipitation / $countPrecipitation : 0,
            'wind' => $countWindSpeed > 0 ? $sumWindSpeed / $countWindSpeed : 0,
            'created_at' => $date,
            'updated_at' => Carbon::now()
        ];
    }

    /**
     * @param $hours
     * @return array
     */
    public static function betweenPreviousHours($hours): array
    {

        $currentTime = Carbon::now()->startOfHour();
        $endTime = $currentTime->copy()->subHours($hours);
        $startTime = $endTime->copy()->subHours();

        return [
            'start' => $startTime,
            'end' => $endTime,
        ];
    }


    /**
     * @param $days
     * @return array
     */
    public static function betweenPreviousDays($days): array
    {

        $today = Carbon::now()->startOfDay();
        $end = $today->copy()->subDays($days);
        $start = $end->copy()->subDay();

        return [
            'start' => $start,
            'end' => $end,
        ];
    }


    /**
     * @param $days
     * @return array
     */
    public static function betweenPreviousWeeks($days): array
    {

        $today = Carbon::now()->startOfDay();
        $end = $today->copy()->subDays($days);
        $start = $end->copy()->subWeek();

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * @param $days
     * @return array
     */
    public static function betweenPreviousMonths($days): array
    {

        $today = Carbon::now()->startOfDay();
        $end = $today->copy()->subDays($days);
        $start = $end->copy()->subMonth();

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * @param $days
     * @return array
     */
    public static function betweenPreviousYears($days): array
    {

        $today = Carbon::now()->startOfDay();
        $end = $today->copy()->subDays($days);
        $start = $end->copy()->subYear();

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * @param $minutes
     * @return array
     */
    public static function betweenPreviousFifteenMins($minutes): array
    {

        $today = Carbon::now();
        $end = $today->copy()->subMinutes($minutes);
        $start = $end->copy()->subMinutes(15);

        return [
            'start' => $start,
            'end' => $end,
        ];

    }

    /**
     * @param $data
     * @param $class
     * @return void
     */
    public static function log($data, $class): void
    {
        Log::info('http request', ['method' => \request()->method(),
            'url' => \request()->fullUrl(),
            'inputs' => $data,
            'created_at' => Carbon::now() . "",
            'client_ip' => \request()->getClientIp(),
            'user_id' => Session::get('CURRENT_USER_ID'),
            'invoked' => $class . ':' . \request()->route()->getActionMethod(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public static function convertLocalTimeStampToUtc($localDate): string
    {
        $dateString = gmdate("Y-m-d H:i:s", ($localDate - 25569) * 86400);
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateString, new DateTimeZone('UTC'));

        return self::convertLocalToUtc($dateTime->format('Y-m-d H:i:s'),Utility::$sriLankaTimeZone);
    }

    public static function excelDateToCarbon($excelDate): bool|Carbon
    {
        if(!$excelDate || !is_numeric($excelDate)){
            throw new InvalidArgumentException("Invalid period start time found in array: $excelDate");
        }
        $date = Carbon::create(1899, 12, 30)->addDays($excelDate);

        $daysPart = floor($excelDate ?? 0);
        $timeFraction = $excelDate - $daysPart;

        if ($timeFraction > 0) {
            $date->addSeconds($timeFraction * 86400); // 86400 seconds in a day
        }

        return $date;
    }

    /**
     * @param $localDate
     * @param $localZone
     * @return string
     */
    public static function convertLocalToUtc($localDate, $localZone): string
    {
        $dateInUTC = Carbon::createFromFormat('Y-m-d H:i:s', $localDate, $localZone) // Replace with your local timezone
        ->setTimezone('UTC');

        return $dateInUTC->toDateTimeString(); // Outputs: 2024-09-30 18:30:0

    }

    /**
     * @param $createdAt
     * @return string
     */
    public static function formatDateTimeEchoTech($createdAt){

        $year = '20' . substr($createdAt, 1, 2);
        $month = substr($createdAt, 3, 2);
        $day = substr($createdAt, 5, 2);
        $hour = substr($createdAt, 7, 2);
        $minutes = substr($createdAt, 9, 2);

        $time = ($year."-".$month."-".$day." ".$hour.":".$minutes.":00");
        return self::convertLocalToUtc($time,Utility::$sriLankaTimeZone);


    }

    /**
     * @param array $data
     * @param $id
     * @param $type
     * @return array
     */
    public static function formatOptionalData(array $data, $id, $type = null): array
    {
        $format = [
            'sensor_location_id' => $data['sensor_location_id'] ?? null,
            'status' => Utility::$statusActive,
            'at' => $data['at'] ?? null,
            'rh' => $data['rh'] ?? null,
            'bp' => $data['bp'] ?? null,
            'nc_0_5' => $data['nc_0_5'] ?? null,
            'nc_1_0' => $data['nc_1_0'] ?? null,
            'nc_2_5' => $data['nc_2_5'] ?? null,
            'nc_4_0' => $data['nc_4_0'] ?? null,
            'nc_10' => $data['nc_10'] ?? null,
            'o3_flow' => $data['o3_flow'] ?? null,
            'o3_gas_press' => $data['o3_gas_press'] ?? null,
            'o3_amb_press' => $data['o3_amb_press'] ?? null,
            'o3_chass_temp' => $data['o3_chass_temp'] ?? null,
            'o3_lamp_curr' => $data['o3_lamp_curr'] ?? null,
            'o3_pga_gain' => $data['o3_pga_gain'] ?? null,
            'solar_rad' => $data['solar_rad'] ?? null,
            'rain_gauge' => $data['rain_gauge'] ?? null,
            'zero_v_ref' => $data['zero_v_ref'] ?? null,
            'two_five_v_ref' => $data['two_five_v_ref'] ?? null,
            'five_v_ref' => $data['five_v_ref'] ?? null,
            'ws_raw' => $data['ws_raw'] ?? null,
            'wd_raw' => $data['wd_raw'] ?? null,
            'room_temp' => $data['room_temp'] ?? null,
            'room_rh' => $data['room_rh'] ?? null,
            'ws_average' => $data['ws_average'] ?? null,
            'wd_average' => $data['wd_average'] ?? null,
            'sigma' => $data['sigma'] ?? null,
            'hto_alarm' => $data['hto_alarm'] ?? null,
            'co_flow' => $data['co_flow'] ?? null,
            'co_gas_press' => $data['co_gas_press'] ?? null,
            'co_amb_press' => $data['co_amb_press'] ?? null,
            'co_cell_temp' => $data['co_cell_temp'] ?? null,
            'co_chass_temp' => $data['co_chass_temp'] ?? null,
            'co_ref_volt' => $data['co_ref_volt'] ?? null,
            'co_input_pot' => $data['co_input_pot'] ?? null,
            'co_pga_gain' => $data['co_pga_gain'] ?? null,
            'co_cooler_volt' => $data['co_cooler_volt'] ?? null,
            'co_bgd_volt' => $data['co_bgd_volt'] ?? null,
            'nox' => $data['nox'] ?? null,
            'nox_flow' => $data['nox_flow'] ?? null,
            'nox_gas_press' => $data['nox_gas_press'] ?? null,
            'no' => $data['no'] ?? null,
            'no_amb_press' => $data['no_amb_press'] ?? null,
            'no_chass_temp' => $data['no_chass_temp'] ?? null,
            'no_cooler_temp' => $data['no_cooler_temp'] ?? null,
            'no_pmt_hv' => $data['no_pmt_hv'] ?? null,
            'no_bgd_volt' => $data['no_bgd_volt'] ?? null,
            'no_hv_adjust' => $data['no_hv_adjust'] ?? null,
            'voc' => $data['voc'] ?? null,
            'voc_sample_flow' => $data['voc_sample_flow'] ?? null,
            'voc_fuel_flow' => $data['voc_fuel_flow'] ?? null,
            'voc_air_flow' => $data['voc_air_flow'] ?? null,
            'voc_det_temp' => $data['voc_det_temp'] ?? null,
            'voc_oxid_temp' => $data['voc_oxid_temp'] ?? null,
            'pm2_5_qtotal' => $data['pm2_5_qtotal'] ?? null,
            'pm2_5_rh' => $data['pm2_5_rh'] ?? null,
            'pm2_5_at' => $data['pm2_5_at'] ?? null,
            'pm2_5_maint' => $data['pm2_5_maint'] ?? null,
            'pm2_5_pwr_fail' => $data['pm2_5_pwr_fail'] ?? null,
            'pm2_5_ref_memb' => $data['pm2_5_ref_memb'] ?? null,
            'pm2_5_nz_alarm' => $data['pm2_5_nz_alarm'] ?? null,
            'pm2_5_flow_alarm' => $data['pm2_5_flow_alarm'] ?? null,
            'pm2_5_press_alarm' => $data['pm2_5_press_alarm'] ?? null,
            'pm2_5_span_chk' => $data['pm2_5_span_chk'] ?? null,
            'pm2_5_cnt_alarm' => $data['pm2_5_cnt_alarm'] ?? null,
            'pm2_5_tape_alarm' => $data['pm2_5_tape_alarm'] ?? null,
            'pm10_qtotal' => $data['pm10_qtotal'] ?? null,
            'pm10_rh' => $data['pm10_rh'] ?? null,
            'pm10_at' => $data['pm10_at'] ?? null,
            'pm10_maint' => $data['pm10_maint'] ?? null,
            'pm10_pwr_fail' => $data['pm10_pwr_fail'] ?? null,
            'pm10_ref_memb' => $data['pm10_ref_memb'] ?? null,
            'pm10_nz_alarm' => $data['pm10_nz_alarm'] ?? null,
            'pm10_flow_alarm' => $data['pm10_flow_alarm'] ?? null,
            'pm10_press_alarm' => $data['pm10_press_alarm'] ?? null,
            'pm10_span_chk' => $data['pm10_span_chk'] ?? null,
            'pm10_cnt_alarm' => $data['pm10_cnt_alarm'] ?? null,
            'pm10_tape_alarm' => $data['pm10_tape_alarm'] ?? null,
            'cal_point' => $data['cal_point'] ?? null,
            'nm' => $data['nm'] ?? null,
            'nm_det_curr' => $data['nm_det_curr'] ?? null,
            'so2_flow' => $data['so2_flow'] ?? null,
            'so2_gas_press' => $data['so2_gas_press'] ?? null,
            'so2_amb_press' => $data['so2_amb_press'] ?? null,
            'so2_chass_temp' => $data['so2_chass_temp'] ?? null,
            'so2_cooler_temp' => $data['so2_cooler_temp'] ?? null,
            'so2_ref_gain' => $data['so2_ref_gain'] ?? null,
            'so2_lamp_curr' => $data['so2_lamp_curr'] ?? null,
            'so2_pmt_hv' => $data['so2_pmt_hv'] ?? null,
            'so2_ref_volt' => $data['so2_ref_volt'] ?? null,
            'so2_instr_gain' => $data['so2_instr_gain'] ?? null,
            'methane_conc' => $data['methane_conc'] ?? null,
            'methane_det_curr' => $data['methane_det_curr'] ?? null,
            'thc' => $data['thc'] ?? null,
            'ch4' => $data['ch4'] ?? null,
            'tvoc' => $data['tvoc'] ?? null,
            'created_at' => $data['created_at'] ?? Carbon::now(),
            'updated_at' =>  $data['updated_at'] ?? Carbon::now(),
            "identifier" => $data['identifier'] ?? null
        ];
        if($type == 'hourly' ){
            $format['hourly_sensor_data_id'] = $id;
        }else{
            $format['sensor_data_id'] = $id;
        }
        return $format;

    }

    /**
     * @param $data
     * @param $sensorLocationId
     * @param $identifier
     * @return array
     */
    public static function sensorDataFormat($data, $sensorLocationId, $identifier = null): array
    {
        return  [
            "sensor_location_id" => $sensorLocationId,
            "PM2.5" => $data['PM2.5 Conc'] ?? $data['PM2.5'] ?? null,
            "PM10" => $data['PM10 Conc'] ?? $data['PM10'] ?? null,
            "CO" => $data['CO Conc'] ?? $data['CO'] ?? null,
            "O3" => $data['O3 Conc'] ?? $data['O3'] ?? null,
            "NO2" => $data['NO2 Conc'] ?? $data['NO2'] ?? null,
            "SO2" => $data['SO2 Conc'] ?? $data['SO2'] ?? null,
            "CO2" => $data['CO2 Conc'] ?? $data['CO2'] ?? null,
            "PM1" => $data['PM1 Conc'] ?? $data['PM1'] ?? null,
            "PM100" => $data['PM100 Conc'] ?? $data['PM100'] ?? null,
            "temperature" => $data['Temperature'] ?? null,
            "humidity" => $data['Humidity'] ?? null,
            "created_at" => $data['created_at'],
            "updated_at" => $data['updated_at'],
            "identifier" => $identifier
        ];
    }

    /**
     * @param $data
     * @param $sensorLocationId
     * @param $identifier
     * @return array
     */
    public static function weatherDataFormat($data, $sensorLocationId, $identifier = null): array
    {

        self::validateNumericArray([
            $data['Rain Gauge'] ?? $data['precipitation'] ?? null,
            $data['WS Average'] ?? $data['WS Raw'] ?? $data['wind'] ?? $data['Fan Speed'] ?? $data['Wind Speed'] ?? null,
            $data['Temperature'] ?? $data['temperature'] ?? null,
            $data['Pressure'] ?? $data['pressure'] ?? $data['BP'] ?? null,
            $data['Humidity'] ?? $data['humidity'] ?? null
        ]);
        return  [
            "sensor_location_id" => $sensorLocationId,
            "precipitation" => $data['Rain Gauge'] ?? $data['precipitation'] ?? null, // TODO:: NEED TO CONFIRM
            "wind" => $data['WS Average'] ?? $data['WS Raw'] ?? $data['wind'] ?? $data['Fan Speed'] ?? $data['Wind Speed'] ?? null, // TODO:: NEED TO CONFIRM
            "temperature" => $data['Temperature'] ?? $data['temperature'] ?? null, // TODO:: NEED TO CONFIRM
            "pressure" => $data['Pressure'] ?? $data['pressure'] ?? $data['BP'] ?? null, // TODO:: NEED TO CONFIRM
            "humidity" => $data['Humidity'] ?? $data['humidity'] ?? null, // TODO:: NEED TO CONFIRM
            "cloud" => $data['Cloud'] ?? $data['cloud'] ?? null, // TODO:: NEED TO CONFIRM
            "created_at" => $data['created_at'],
            "updated_at" => $data['updated_at']
        ];
    }

    /**
     * @param $data
     * @param $sensorLocationId
     * @param $identifier
     * @return array
     */
    public static function optionalDataFormat($data, $sensorLocationId, $identifier = null): array
    {
        self::validateNumericArray([
            $data['AT'] ?? null,
            $data['BP'] ?? null,
            $data['RH'] ?? null,
            $data['Solar Rad'] ?? null,
            $data['Rain Gauge'] ?? null,
            $data['WS Raw'] ?? null,
            $data['WD Raw'] ?? null,
            $data['WS Average'] ?? null,
            $data['WD Average'] ?? null,
            $data['NO Conc'] ?? null,
            $data['VOC'] ?? null,
            $data['NOx Conc'] ?? null
        ]);
        return [
            'sensor_location_id' => $sensorLocationId,
            'at' => $data['AT'] ?? null,
            'bp' => $data['BP'] ?? null,
            'rh' => $data['RH'] ?? null,
            'solar_rad' => $data['Solar Rad'] ?? null,
            'rain_gauge' => $data['Rain Gauge'] ?? null,
            'ws_raw' => $data['WS Raw'] ?? null,
            'wd_raw' => $data['WD Raw'] ?? null,
            'ws_average' => $data['WS Average'] ?? null,
            'wd_average' => $data['WD Average'] ?? null,
            'no' => $data['NO Conc'] ?? null,
            'voc' => $data['VOC'] ?? null,
            'nox' => $data['NOx Conc'] ?? null,
            "created_at" => $data['created_at'],
            "updated_at" => $data['updated_at'],
            "identifier" => $identifier,
        ];
    }

    /**
     * @param $array
     * @return void
     */
    public static function validateNumericArray($array): void
    {
        $nonNumericValues = array_filter($array, fn($value) => !is_null($value) && !is_numeric($value));

        if (!empty($nonNumericValues)) {
            $values = implode(", ", $nonNumericValues);
            throw new InvalidArgumentException("Non-numeric values found in array: $values");
        }
    }
}
