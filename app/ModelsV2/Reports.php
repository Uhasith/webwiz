<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $report_template_id
 * @property integer $sensor_location_id
 * @property integer $sensor_data_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property SensorDatas $sensorData
 * @property ReportTemplate $reportTemplate
 * @property SensorLocations $sensorLocation
 */
class Reports extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['report_template_id', 'sensor_location_id', 'sensor_data_id', 'status', 'created_at', 'updated_at'];

    /**
     * @return BelongsTo
     */
    public function sensorData(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\SensorDatas');
    }

    /**
     * @return BelongsTo
     */
    public function reportTemplate(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\ReportTemplate');
    }

    /**
     * @return BelongsTo
     */
    public function sensorLocation(): BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\SensorLocations');
    }
}
