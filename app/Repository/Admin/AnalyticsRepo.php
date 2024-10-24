<?php

namespace App\Repository\Admin;

use App\Helpers\Utility;
use App\ModelsV2\Locations;
use App\ModelsV2\SensorDatas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsRepo
{
    public function getTop10Cities($sensorId, $ranking, $start, $end,$period)
    {
        $query = Locations::whereHas('sensorLocations', function ($query) use ($sensorId, $ranking) {
            $query->where('status', Utility::$statusActive)
                ->where('sensor_id', $sensorId)
                ->whereHas('sensorDatas', function ($query) use ($ranking) {
                    $query->where('status', Utility::$statusActive)
                        ->where('aqi->SL', ucfirst($ranking));
                });
        })
            ->with(['sensorLocations' => function ($query) use ($sensorId, $ranking,$start,$end,$period) {
                $query->where('status', Utility::$statusActive);
                $query->where('sensor_id', $sensorId);
                if($period == 'Annually'){
                    $query->withCount(['annuallySensorData as sensor_data_Count' => function ($query) use ($ranking,$start,$end) {
                        $query->where('status', Utility::$statusActive);
                        $query->whereBetween('created_at', [$start,$end]);
                        $query->where('aqi->SL', ucfirst($ranking));
                    }]);
                }else{
                    $query->withCount(['monthlySensorData as sensor_data_Count' => function ($query) use ($ranking,$start,$end) {
                        $query->where('status', Utility::$statusActive);
                        $query->whereBetween('created_at', [$start,$end]);
                        $query->where('aqi->SL', ucfirst($ranking));
                    }]);
                }
            }]);

        return $query->get();

    }
}
