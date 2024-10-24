<?php

namespace App;

use App\Helpers\Utility;
use App\ModelsV2\SensorDatas;
use Illuminate\Support\Facades\Log;

class AqiService
{
    /**
     * @param $params
     * @param $status
     * @return array|void
     */
    public function generateAqi($params, $status)
    {
        $aqiPath = base_path('aqi_data.json');
        if (file_exists($aqiPath)) {
            $data = json_decode(file_get_contents($aqiPath), true);
            $breakpoints = $data['breakpoints'];

            $aqiPm2_5 = null;
            $aqiPm10 = null;
            $aqiO3 = null;
            $aqiCO = null;
            $aqiNO2 = null;
            $aqiSO2 = null;
            $aqiCO2 = null;

            // calculate sub aqi values
            if(isset($params['PM2.5']) && $status['PM2.5']==Utility::$statusActive){
            $aqiPm2_5 = $this->subAqi($breakpoints['PM2.5'], $params['PM2.5'], $data);
            }
            if(isset($params['PM10']) && $status['PM10']==Utility::$statusActive){
            $aqiPm10 = $this->subAqi($breakpoints['PM10'], $params['PM10'], $data);
            }
            if(isset($params['O3']) && $status['O3']==Utility::$statusActive){
            $aqiO3 = $this->subAqi($breakpoints['O3'], $params['O3'], $data);
            }
            if(isset($params['CO']) && $status['CO']==Utility::$statusActive){
            $aqiCO = $this->subAqi($breakpoints['CO'], $params['CO'], $data);
            }
            if(isset($params['NO2']) && $status['NO2']==Utility::$statusActive){
            $aqiNO2 = $this->subAqi($breakpoints['NO2'], $params['NO2'], $data);
            }
            if(isset($params['SO2']) && $status['SO2']==Utility::$statusActive){
            $aqiSO2 = $this->subAqi($breakpoints['SO2'], $params['SO2'], $data);
            }

            return [
                "AQI" => $this->calculateMax([$aqiPm2_5, $aqiPm10, $aqiO3, $aqiCO, $aqiNO2, $aqiSO2, $aqiCO2],$data),
                "PM2.5" => $aqiPm2_5,
                "PM10" => $aqiPm10,
                "O3" => $aqiO3,
                "CO" => $aqiCO,
                "NO2" => $aqiNO2,
                "SO2" => $aqiSO2,
                "CO2" => $aqiCO2
            ];

        }
        Log::error("Failed to fetch aqi data file");

    }

    // AQI calculation for each param

    /**
     * @param $bpLo
     * @param $bpHi
     * @param $value
     * @param $ILo
     * @param $IHi
     * @return float|int
     */
    private function calculateAqi($bpLo, $bpHi, $value, $ILo, $IHi): float|int
    {
        return (($value - $bpLo) * (($IHi - $ILo) / ($bpHi - $bpLo))) + $ILo;
    }

    // Calculate final AQI

    /**
     * @param $subAqiValues
     * @param $data
     * @return array|null
     */
    private function calculateMax($subAqiValues, $data): ?array
    {
        //check if array contains only null elements
        if (count(array_filter($subAqiValues, function($value) { return $value !== null; })) === 0) {
            return null;
        }
        return $this->aqiJson($data, max(array_column($subAqiValues, "AQI")));
    }

    // Calculate sub AQI for each

    /**
     * @param $breakpoint
     * @param $param
     * @param $data
     * @return array|void
     */
    private function subAqi($breakpoint, $param, $data)
    {
        foreach (array_keys($breakpoint) as $key) {
            if ($breakpoint[$key]["lo"] == null && $breakpoint[$key]["hi"] == null) {
                return $this->aqiJson($data, null);
            }
            $startRange = explode("-", $key)[0];
            $endRange = explode("-", $key)[1];
            if ($param >= $startRange && $param < $endRange) {
                $result = $this->calculateAqi( $startRange, $endRange, $param,$breakpoint[$key]["lo"], $breakpoint[$key]["hi"]);
                return $this->aqiJson($data, $result);
            }
        }
    }

    /**
     * @param $data
     * @param $aqi
     * @return array
     */
    public function aqiJson($data, $aqi): array
    {
        return [
            "AQI" => (string)round($aqi),
            "SL" => $this->status($data['sl']['range'], $aqi),
            "US" => $this->status($data['us']['range'], $aqi),
            "IN" => $this->status($data['in']['range'], $aqi),
            "CN" => $this->status($data['cn']['range'], $aqi)
        ];
    }

    // Determine status

    /**
     * @param $range
     * @param $aqi
     * @return mixed|void
     */
    private function status($range, $aqi)
    {
        foreach (array_keys($range) as $key) {
            $startRange = explode("-", $key)[0];
            $endRange = explode("-", $key)[1];
            if ($aqi >= $startRange && $aqi < $endRange) {
                return $range[$key]['status'];
            }
        }
    }


    /**
     * @param $data
     * @param $dataType
     * @return array
     * @throws \Exception
     */
    public function processAirQualityData($data, $dataType,$actionType = null): array
    {
        $dataStatus = Utility::validateAirQualityData($data);
        $subAqis = [];
        $tableStatus = Utility::$statusPending;

        if ( $dataStatus['PM2.5'] == Utility::$statusActive || $dataStatus['PM10'] == Utility::$statusActive) {
            $subAqis = $this->generateAqi($data, $dataStatus);
            $tableStatus = Utility::$statusActive;
        }

        return SensorDatas::sensorDataFormat($data,$subAqis,$dataStatus,$tableStatus,$dataType,$actionType);

    }
}
