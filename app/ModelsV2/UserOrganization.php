<?php

namespace App\ModelsV2;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $organization_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 */
class UserOrganization extends Model
{
     /**
     * @var array
     */
    protected $table = 'user_organization';
    protected $fillable = ['user_id', 'organization_id', 'status', 'created_at', 'updated_at'];


    /**
     * @return BelongsTo
     */
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * @return BelongsTo
     */
    public function organizations(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\Organization');
    }


}
