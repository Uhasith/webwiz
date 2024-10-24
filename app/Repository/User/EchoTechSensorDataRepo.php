<?php

namespace App\Repository\User;

use App\ModelsV2\EchoTechSensorData;

class EchoTechSensorDataRepo
{

    /**
     * @param array $echoTechData
     * @return void
     */
    public function saveEchoTechDataList(array $echoTechData): void
    {
        EchoTechSensorData::upsert($echoTechData,['identifier'],array_keys($echoTechData[0] ?? []));
    }
}
