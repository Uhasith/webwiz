<?php

namespace App\Repository\User;

use App\ModelsV2\Locations;

class LocationsRepo{


    public function getAllSensorLocations(){
        return Locations::whereHas('sensorLocations')->with('district.province')->get();
    }

    public function getLocationsQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Locations::with(['district.province']);
    }

    public function searchLocations($query, $searchTerm)
    {
        return $query->where('name', 'LIKE', "%{$searchTerm}%");
    }

}
