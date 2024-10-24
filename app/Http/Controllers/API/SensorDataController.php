<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SensorData;
use App\Http\Resources\SensorDataResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    public function getRecentData(Request $request)
    {
        $minutes = $request->input('minutes', 5); // Default to 5 minutes if not specified
        $fiveMinutesAgo = Carbon::now()->subMinutes($minutes);
        
        $recentData = SensorData::where('updated_at', '>=', $fiveMinutesAgo)
            ->with(['sensorLocation.sensor', 'sensorLocation.province'])
            ->latest()
            ->get();
        
        return SensorDataResource::collection($recentData);
    }

    
}