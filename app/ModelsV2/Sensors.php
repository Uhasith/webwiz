<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $equipment_type_id
 * @property integer $organization_id
 * @property string $name
 * @property string $status
 * @property string $slug
 * @property string $created_at
 * @property string $updated_at
 * @property EquipmentType $equipmentType
 * @property SensorLocations[] $sensorLocations
 */
class Sensors extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['equipment_type_id','organization_id', 'name', 'status','slug', 'created_at', 'updated_at'];

    /**
     * @return BelongsTo
     */
    public function equipmentType(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\EquipmentType');
    }

    /**
     * @return BelongsTo
     */
    public function organizations(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\Organization');
    }

    /**
     * @return HasMany
     */

    public function sensorLocations(): HasMany
    {
        return $this->hasMany('App\ModelsV2\SensorLocations','sensor_id');
    }

}
