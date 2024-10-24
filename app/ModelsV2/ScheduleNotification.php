<?php

namespace App\ModelsV2;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property mixed $sender_id
 * @property false|mixed|string $receiver_id
 * @property mixed $type
 * @property mixed $title
 * @property mixed $description
 * @property mixed $scheduled_at
 * @property mixed|string $status
 */
class ScheduleNotification extends Model
{

    protected $table = 'schedule_notification';
    /**
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'type',
        'status',
        'title',
        'description',
        'scheduled_at'
    ];

    /**
     * @return BelongsTo
     */
    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}
