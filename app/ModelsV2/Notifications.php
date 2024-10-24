<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $receiver_id
 * @property string $type
 * @property string $title
 * @property string $description
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 **/
class Notifications extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'receiver_id', 'type', 'title', 'description', 'status', 'created_at', 'updated_at'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
