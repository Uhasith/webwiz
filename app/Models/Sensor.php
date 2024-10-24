<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'equipment_type_id']; // Add other fields as needed

    public function sensorLocations()
    {
        return $this->hasMany(SensorLocation::class);
    }

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class);
    }
}