<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property string $province_name
 * @property string $province_key
 * @property string $created_at
 * @property string $updated_at
 * @property Districts[] $districts
 */
class Province extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['province_name', 'province_key', 'created_at', 'updated_at'];

    /**
     * @return HasMany
     */
    public function districts(): HasMany
    {
        return $this->hasMany('App\ModelsV2\Districts');
    }
}
