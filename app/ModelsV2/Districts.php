<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $province_id
 * @property string $district_name
 * @property string $created_at
 * @property string $updated_at
 * @property Province $province
 * @property Locations[] $locations
 */
class Districts extends Model
{

    protected $table = "districts";
    /**
     * @var array
     */
    protected $fillable = ['province_id', 'district_name', 'created_at', 'updated_at'];

    /**
     * @return BelongsTo
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\Province');
    }

    /**
     * @return HasMany
     */
    public function locations(): HasMany
    {
        return $this->hasMany('App\ModelsV2\Locations');
    }
}
