<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalAccess extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'external_access';

    /**
     * @var array
     */
    protected $fillable = ['name', 'status', 'api_key', 'whitelisted_ips', 'locations', 'sensors', 'recent_data', 'hourly_data', 'monthly_data', 'annually_data', 'deleted_at', 'created_at', 'updated_at'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [];

}
