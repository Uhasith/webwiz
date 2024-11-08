<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    use HasFactory;

    protected $fillable = ['type_name'];

    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }
}