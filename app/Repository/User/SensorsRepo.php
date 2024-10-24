<?php

namespace App\Repository\User;

use App\ModelsV2\Sensors;

class SensorsRepo
{

    public function getSensorsByEquipmentId($id)
    {
        return Sensors::where('equipment_type_id', $id)->get();
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Sensors::all();
    }


    public function getSensorsByLocationsId($id)
    {
        return Sensors::whereHas('sensorLocations', function ($query) use ($id) {
            $query->where('location_id', $id);
        })->get();
    }

    public function getIdBySlug($slug){

        return Sensors::with('sensorLocations.location')
            ->where('slug', $slug)->first();

    }
}
