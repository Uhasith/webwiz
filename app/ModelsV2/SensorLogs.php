<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $sensor_location_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property SensorLocations $sensorLocation
 */
class SensorLogs extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['sensor_location_id', 'status', 'created_at', 'updated_at'];

    /**
     * @return BelongsTo
     */
    public function sensorLocation(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\SensorLocations');
    }
}
