<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Http\Resources\SensorResource;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function index(Request $request)
    {
        $equipmentType = $request->query('equipment_type');

        $query = Sensor::query();

        if ($equipmentType && $equipmentType !== 'all') {
            $query->whereHas('equipmentType', function ($q) use ($equipmentType) {
                $q->where('id', $equipmentType);
            });
        }

        $sensors = $query->get();
        return SensorResource::collection($sensors);
    }
}