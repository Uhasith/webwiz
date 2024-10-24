<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorLocation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'latitude',
        'longitude',
        'location_name',
        'sensor_id',
        'district_id',
        'locationindex',
        'status',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }

}
