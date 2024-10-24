<?php

namespace App\ModelsV2;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property string $name
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class Organization extends Model
{

    protected $table = 'organization';
    /**
     * @var array
     */
    protected $fillable = ['name','status', 'created_at', 'updated_at'];


    /**
     * @return HasMany
     */
    public function sensors(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\ModelsV2\Sensors');
    }

    /**
     * @return HasMany
     */
    public function userOrganizations(): HasMany
    {
        return $this->hasMany('App\ModelsV2\UserOrganization','organization_id');
    }

}
