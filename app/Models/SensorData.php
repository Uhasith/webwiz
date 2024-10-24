<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $table = 'sensor_datas';
    
    use HasFactory;

    protected $fillable = [
        'sensor_location_id',
        'status',
        'pm2_5',
        'pm10',
        'o3',
        'co',
        'no2',
        'so2',
        'co2',
        'pm2_5_aqi',
        'pm10_aqi',
        'o3_aqi',
        'co_aqi',
        'no2_aqi',
        'so2_aqi',
        'co2_aqi',
    ];

    public function sensorLocation()
    {
        return $this->belongsTo(SensorLocation::class);
    }

    
}