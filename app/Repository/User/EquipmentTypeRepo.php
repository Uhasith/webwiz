<?php

namespace App\Repository\User;

use App\ModelsV2\EquipmentType;

class EquipmentTypeRepo
{
    public function getAllEquipmentTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return EquipmentType::all();
    }
}
