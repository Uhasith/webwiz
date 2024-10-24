<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SensorDataResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'pm2_5' => $this->pm2_5,
            'pm10' => $this->pm10,
            'o3' => $this->o3,
            'co' => $this->co,
            'no2' => $this->no2,
            'so2' => $this->so2,
            'co2' => $this->co2,
            'pm2_5_aqi' => $this->pm2_5_aqi,
            'pm10_aqi' => $this->pm10_aqi,
            'o3_aqi' => $this->o3_aqi,
            'co_aqi' => $this->co_aqi,
            'no2_aqi' => $this->no2_aqi,
            'so2_aqi' => $this->so2_aqi,
            'co2_aqi' => $this->co2_aqi,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sensor_location' => new SensorLocationResource($this->whenLoaded('sensorLocation')),
        ];
    }
}