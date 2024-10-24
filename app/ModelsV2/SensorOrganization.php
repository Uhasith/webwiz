<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property Sensors $sensor
 */
class SensorOrganization extends Model
{
    /**
     * @var array
     */
    protected $table = "sensor_organization";
    protected $fillable = ['sensor_id','organization_id'];

    /**
     * @return BelongsTo
     */
    public function sensors(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\Sensors','sensor_id');
    }

    /**
     * @return BelongsTo
     */
    public function organizations(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\Organization','organization_id');
    }

}
