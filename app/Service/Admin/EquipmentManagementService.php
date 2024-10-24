<?php

namespace App\Service\Admin;

use App\Http\Resources\SensorResource;
use App\Repository\Admin\SensorRepo;

class EquipmentManagementService{
    protected SensorRepo $sensorRepository;

    /**
     * @param SensorRepo $sensorRepository
     */
    public function __construct(SensorRepo $sensorRepository)
    {
        $this->sensorRepository = $sensorRepository;
    }

    public function getPaginatedUsersWithRoles($perPage = 10): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $sensors = $this->sensorRepository->getPaginatedSensors($perPage);
        return SensorResource::collection($sensors);
    }
}
