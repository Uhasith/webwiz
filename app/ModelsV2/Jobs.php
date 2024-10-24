<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $queue
 * @property string $payload
 * @property boolean $attempts
 * @property boolean $reserved
 * @property int $reserved_at
 * @property int $available_at
 * @property int $created_at
 */
class Jobs extends Model
{
    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['queue', 'payload', 'attempts', 'reserved', 'reserved_at', 'available_at', 'created_at'];
}
