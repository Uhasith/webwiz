<?php

namespace App\Repository\User;

use App\ModelsV2\FifteenMinuteSensorData;

class FifteenMinuteDataRepo{

    public function saveFifteenMinuteSensorDataList(array $sensorData){
        return FifteenMinuteSensorData::insert($sensorData);
    }

}
