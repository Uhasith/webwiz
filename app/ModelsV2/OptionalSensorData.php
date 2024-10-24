<?php

namespace App\ModelsV2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $sensor_location_id
 * @property integer $sensor_data_id
 * @property string $status
 * @property float $at
 * @property float $rh
 * @property float $bp
 * @property float $nc_0_5
 * @property float $nc_1_0
 * @property float $nc_2_5
 * @property float $nc_4_0
 * @property float $nc_10
 * @property float $o3_flow
 * @property float $o3_gas_press
 * @property float $o3_amb_press
 * @property float $o3_chass_temp
 * @property float $o3_lamp_curr
 * @property float $o3_pga_gain
 * @property float $solar_rad
 * @property float $rain_gauge
 * @property float $zero_v_ref
 * @property float $two_five_v_ref
 * @property float $five_v_ref
 * @property float $ws_raw
 * @property float $wd_raw
 * @property float $room_temp
 * @property float $room_rh
 * @property float $ws_average
 * @property float $wd_average
 * @property float $sigma
 * @property float $hto_alarm
 * @property float $co_flow
 * @property float $co_gas_press
 * @property float $co_amb_press
 * @property float $co_cell_temp
 * @property float $co_chass_temp
 * @property float $co_ref_volt
 * @property float $co_input_pot
 * @property float $co_pga_gain
 * @property float $co_cooler_volt
 * @property float $co_bgd_volt
 * @property float $nox
 * @property float $nox_flow
 * @property float $nox_gas_press
 * @property float $no
 * @property float $no_amb_press
 * @property float $no_chass_temp
 * @property float $no_cooler_temp
 * @property float $no_pmt_hv
 * @property float $no_bgd_volt
 * @property float $no_hv_adjust
 * @property float $voc
 * @property float $voc_sample_flow
 * @property float $voc_fuel_flow
 * @property float $voc_air_flow
 * @property float $voc_det_temp
 * @property float $voc_oxid_temp
 * @property float $pm2_5_qtotal
 * @property float $pm2_5_rh
 * @property float $pm2_5_at
 * @property float $pm2_5_maint
 * @property float $pm2_5_pwr_fail
 * @property float $pm2_5_ref_memb
 * @property float $pm2_5_nz_alarm
 * @property float $pm2_5_flow_alarm
 * @property float $pm2_5_press_alarm
 * @property float $pm2_5_span_chk
 * @property float $pm2_5_cnt_alarm
 * @property float $pm2_5_tape_alarm
 * @property float $pm10_qtotal
 * @property float $pm10_rh
 * @property float $pm10_at
 * @property float $pm10_maint
 * @property float $pm10_pwr_fail
 * @property float $pm10_ref_memb
 * @property float $pm10_nz_alarm
 * @property float $pm10_flow_alarm
 * @property float $pm10_press_alarm
 * @property float $pm10_span_chk
 * @property float $pm10_cnt_alarm
 * @property float $pm10_tape_alarm
 * @property float $cal_point
 * @property float $nm
 * @property float $nm_det_curr
 * @property float $so2_flow
 * @property float $so2_gas_press
 * @property float $so2_amb_press
 * @property float $so2_chass_temp
 * @property float $so2_cooler_temp
 * @property float $so2_ref_gain
 * @property float $so2_lamp_curr
 * @property float $so2_pmt_hv
 * @property float $so2_ref_volt
 * @property float $so2_instr_gain
 * @property float $methane_conc
 * @property float $methane_det_curr
 * @property float $thc
 * @property string $created_at
 * @property string $updated_at
 */

class OptionalSensorData extends Model
{

    protected $table = 'optional_sensor_data';


    protected $fillable = [
        'sensor_location_id',
        'sensor_data_id',
        'status',
        'at',
        'rh',
        'bp',
        'nc_0_5',
        'nc_1_0',
        'nc_2_5',
        'nc_4_0',
        'nc_10',
        'o3_flow',
        'o3_gas_press',
        'o3_amb_press',
        'o3_chass_temp',
        'o3_lamp_curr',
        'o3_pga_gain',
        'solar_rad',
        'rain_gauge',
        'zero_v_ref',
        'two_five_v_ref',
        'five_v_ref',
        'ws_raw',
        'wd_raw',
        'room_temp',
        'room_rh',
        'ws_average',
        'wd_average',
        'sigma',
        'hto_alarm',
        'co_flow',
        'co_gas_press',
        'co_amb_press',
        'co_cell_temp',
        'co_chass_temp',
        'co_ref_volt',
        'co_input_pot',
        'co_pga_gain',
        'co_cooler_volt',
        'co_bgd_volt',
        'nox',
        'nox_flow',
        'nox_gas_press',
        'no',
        'no_amb_press',
        'no_chass_temp',
        'no_cooler_temp',
        'no_pmt_hv',
        'no_bgd_volt',
        'no_hv_adjust',
        'voc',
        'voc_sample_flow',
        'voc_fuel_flow',
        'voc_air_flow',
        'voc_det_temp',
        'voc_oxid_temp',
        'pm2_5_qtotal',
        'pm2_5_rh',
        'pm2_5_at',
        'pm2_5_maint',
        'pm2_5_pwr_fail',
        'pm2_5_ref_memb',
        'pm2_5_nz_alarm',
        'pm2_5_flow_alarm',
        'pm2_5_press_alarm',
        'pm2_5_span_chk',
        'pm2_5_cnt_alarm',
        'pm2_5_tape_alarm',
        'pm10_qtotal',
        'pm10_rh',
        'pm10_at',
        'pm10_maint',
        'pm10_pwr_fail',
        'pm10_ref_memb',
        'pm10_nz_alarm',
        'pm10_flow_alarm',
        'pm10_press_alarm',
        'pm10_span_chk',
        'pm10_cnt_alarm',
        'pm10_tape_alarm',
        'cal_point',
        'nm',
        'nm_det_curr',
        'so2_flow',
        'so2_gas_press',
        'so2_amb_press',
        'so2_chass_temp',
        'so2_cooler_temp',
        'so2_ref_gain',
        'so2_lamp_curr',
        'so2_pmt_hv',
        'so2_ref_volt',
        'so2_instr_gain',
        'methane_conc',
        'methane_det_curr',
        'thc',
    ];



    /**
     * Get the sensor location associated with the sensor data.
     */
    public function sensorLocation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\SensorLocations');
    }

    /**
     * @return BelongsTo
     */
    public function sensorData(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\ModelsV2\SensorDatas');
    }


}
