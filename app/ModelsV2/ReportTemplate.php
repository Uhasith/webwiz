<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $description
 * @property string $status
 * @property string $start_date
 * @property string $end_date
 * @property string $created_at
 * @property string $updated_at
 * @property Reports[] $reports
 * @property User $user
 */
class ReportTemplate extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'name', 'description', 'status', 'start_date', 'end_date', 'created_at', 'updated_at'];

    /**
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany('App\ModelsV2\Reports');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function histories(): HasMany
    {
        return $this->hasMany(ReportHistory::class, 'template_id');
    }
}
