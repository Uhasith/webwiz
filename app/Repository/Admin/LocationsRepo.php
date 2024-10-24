<?php

namespace App\Repository\Admin;

use App\ModelsV2\Locations;

class LocationsRepo
{

    public function findById($locationId)
    {
        return Locations::where('id', $locationId)->with(['district.province'])->first();

    }
}
