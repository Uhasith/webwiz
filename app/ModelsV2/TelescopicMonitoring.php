<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $tag
 */
class TelescopicMonitoring extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'telescope_monitoring';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'tag';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [];
}
