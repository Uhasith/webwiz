<?php

namespace App\ModelsV2;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 */
class UserLogs extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'type', 'description', 'created_at', 'updated_at'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
