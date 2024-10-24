<?php

namespace App\ModelsV2;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
class ReportHistory extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $table = 'report_history';  // Change if the table name is different
    protected $fillable = ['template_id', 'user_id', 'created_at', 'updated_at'];

    /**
     * @return BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
     {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    /**
     * @return HasMany
     */
    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Reports::class, 'history_id');
    }


}
