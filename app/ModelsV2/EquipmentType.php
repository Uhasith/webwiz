<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property string $type_name
 * @property string $created_at
 * @property string $updated_at
 * @property Sensors[] $sensors
 */
class EquipmentType extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['type_name', 'created_at', 'updated_at'];

    /**
     * @return HasMany
     */
    public function sensors(): HasMany
    {
        return $this->hasMany('App\ModelsV2\Sensors');
    }
}
