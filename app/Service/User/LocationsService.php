<?php

namespace App\Service\User;

use App\Repository\User\LocationsRepo;

class LocationsService
{

    private LocationsRepo $locationRepo;

    /**
     * @param LocationsRepo $locationRepo
     */
    public function __construct(LocationsRepo $locationRepo)
    {
        $this->locationRepo = $locationRepo;
    }

    public function getAllSensorLocations()
    {
        return $this->locationRepo->getAllSensorLocations();
    }

    public function getCitiesWithProvince($searchTerm = null, $limit = null)
    {
        $query = $this->locationRepo->getLocationsQuery();

        if ($searchTerm) {
            $query = $this->locationRepo->searchLocations($query, $searchTerm);
        }
        return $query->get()->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name . ', ' . $location->district->province->province_name
            ];
        });
    }
}
