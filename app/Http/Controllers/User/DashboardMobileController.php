<?php

namespace App\Http\Controllers\User;

use App\Helpers\Utility;
use App\Service\User\LocationsService;
use App\Service\User\SensorsService;
use Inertia\Inertia;

class DashboardMobileController
{
    private SensorsService $sensorsService;
    private LocationsService $locationsService;

    /**
     * @param SensorsService $sensorsService
     * @param LocationsService $locationsService
     */
    public function __construct(SensorsService $sensorsService,LocationsService $locationsService)
    {
        $this->sensorsService = $sensorsService;
        $this->locationsService = $locationsService;
    }

    public function index()
    {
        Utility::log([],get_class());
        $equipmentTypes = $this->sensorsService->homepageService();
        $sensors = $this->sensorsService->getSensorsByEquipmentId('all');

        $locations = $this->locationsService->getCitiesWithProvince('');

        return Inertia::render('Userview/MobileCompare', [
            'initialLocations' => $locations,
            'equipmentTypes' => $equipmentTypes,
            'sensors' => $sensors,
            'pollutantParameters' => config('pollutantparameters')
        ]);
    }
}
