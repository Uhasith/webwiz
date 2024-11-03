<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalAccess extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'api_key',
        'whitelisted_ips',
        'locations',
        'sensors',
        'recent_data',
        'hourly_data',
        'daily_data',
        'monthly_data',
        'annually_data',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'locations' => 'array',
        'sensors' => 'array',
        'whitelisted_ips' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'recent_data' => 'boolean',
        'hourly_data' => 'boolean',
        'daily_data' => 'boolean',
        'monthly_data' => 'boolean',
        'annually_data' => 'boolean',
    ];
}
