<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EquipmentTypeResource;
use App\Models\EquipmentType;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    public function index()
    {
        $equipmentTypes = EquipmentType::all();
        return EquipmentTypeResource::collection($equipmentTypes);
    }
}