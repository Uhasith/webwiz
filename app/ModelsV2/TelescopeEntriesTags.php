<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $entry_uuid
 * @property string $tag
 * @property TelescopeEntries $telescopeEntry
 */
class TelescopeEntriesTags extends Model
{
    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @return BelongsTo
     */
    public function telescopeEntry(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\TelescopeEntries', 'entry_uuid', 'uuid');
    }
}
