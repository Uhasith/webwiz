<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $sequence
 * @property string $uuid
 * @property string $batch_id
 * @property string $family_hash
 * @property boolean $should_display_on_index
 * @property string $type
 * @property string $content
 * @property string $created_at
 * @property TelescopeEntriesTags[] $telescopeEntriesTags
 */
class TelescopeEntries extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'sequence';

    /**
     * @var array
     */
    protected $fillable = ['uuid', 'batch_id', 'family_hash', 'should_display_on_index', 'type', 'content', 'created_at'];

    /**
     * @return HasMany
     */
    public function telescopeEntriesTags(): HasMany
    {
        return $this->hasMany('App\ModelsV2\TelescopeEntriesTags', 'entry_uuid', 'uuid');
    }
}
