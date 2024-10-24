<?php

namespace App\Repository\User;

use App\ModelsV2\OptionalSensorData;

class OptionalSensorDataRepo{

    public function save($data): OptionalSensorData
    {

        $optionalSensorData = new OptionalSensorData();
        $optionalSensorData->sensor_location_id = $data['sensor_location_id'] ?? null;
        $optionalSensorData->sensor_data_id = $data['sensor_data_id'] ?? null;
        $optionalSensorData->status = $data['status'] ?? null;
        $optionalSensorData->at = $data['at'] ?? null;
        $optionalSensorData->rh = $data['rh'] ?? null;
        $optionalSensorData->bp = $data['bp'] ?? null;
        $optionalSensorData->nc_0_5 = $data['nc_0_5'] ?? null;
        $optionalSensorData->nc_1_0 = $data['nc_1_0'] ?? null;
        $optionalSensorData->nc_2_5 = $data['nc_2_5'] ?? null;
        $optionalSensorData->nc_4_0 = $data['nc_4_0'] ?? null;
        $optionalSensorData->nc_10 = $data['nc_10'] ?? null;
        $optionalSensorData->o3_flow = $data['o3_flow'] ?? null;
        $optionalSensorData->o3_gas_press = $data['o3_gas_press'] ?? null;
        $optionalSensorData->o3_amb_press = $data['o3_amb_press'] ?? null;
        $optionalSensorData->o3_chass_temp = $data['o3_chass_temp'] ?? null;
        $optionalSensorData->o3_lamp_curr = $data['o3_lamp_curr'] ?? null;
        $optionalSensorData->o3_pga_gain = $data['o3_pga_gain'] ?? null;
        $optionalSensorData->solar_rad = $data['solar_rad'] ?? null;
        $optionalSensorData->rain_gauge = $data['rain_gauge'] ?? null;
        $optionalSensorData->zero_v_ref = $data['zero_v_ref'] ?? null;
        $optionalSensorData->two_five_v_ref = $data['two_five_v_ref'] ?? null;
        $optionalSensorData->five_v_ref = $data['five_v_ref'] ?? null;
        $optionalSensorData->ws_raw = $data['ws_raw'] ?? null;
        $optionalSensorData->wd_raw = $data['wd_raw'] ?? null;
        $optionalSensorData->room_temp = $data['room_temp'] ?? null;
        $optionalSensorData->room_rh = $data['room_rh'] ?? null;
        $optionalSensorData->ws_average = $data['ws_average'] ?? null;
        $optionalSensorData->wd_average = $data['wd_average'] ?? null;
        $optionalSensorData->sigma = $data['sigma'] ?? null;
        $optionalSensorData->hto_alarm = $data['hto_alarm'] ?? null;
        $optionalSensorData->co_flow = $data['co_flow'] ?? null;
        $optionalSensorData->co_gas_press = $data['co_gas_press'] ?? null;
        $optionalSensorData->co_amb_press = $data['co_amb_press'] ?? null;
        $optionalSensorData->co_cell_temp = $data['co_cell_temp'] ?? null;
        $optionalSensorData->co_chass_temp = $data['co_chass_temp'] ?? null;
        $optionalSensorData->co_ref_volt = $data['co_ref_volt'] ?? null;
        $optionalSensorData->co_input_pot = $data['co_input_pot'] ?? null;
        $optionalSensorData->co_pga_gain = $data['co_pga_gain'] ?? null;
        $optionalSensorData->co_cooler_volt = $data['co_cooler_volt'] ?? null;
        $optionalSensorData->co_bgd_volt = $data['co_bgd_volt'] ?? null;
        $optionalSensorData->nox = $data['nox'] ?? null;
        $optionalSensorData->nox_flow = $data['nox_flow'] ?? null;
        $optionalSensorData->nox_gas_press = $data['nox_gas_press'] ?? null;
        $optionalSensorData->no = $data['no'] ?? null;
        $optionalSensorData->no_amb_press = $data['no_amb_press'] ?? null;
        $optionalSensorData->no_chass_temp = $data['no_chass_temp'] ?? null;
        $optionalSensorData->no_cooler_temp = $data['no_cooler_temp'] ?? null;
        $optionalSensorData->no_pmt_hv = $data['no_pmt_hv'] ?? null;
        $optionalSensorData->no_bgd_volt = $data['no_bgd_volt'] ?? null;
        $optionalSensorData->no_hv_adjust = $data['no_hv_adjust'] ?? null;
        $optionalSensorData->voc = $data['voc'] ?? null;
        $optionalSensorData->voc_sample_flow = $data['voc_sample_flow'] ?? null;
        $optionalSensorData->voc_fuel_flow = $data['voc_fuel_flow'] ?? null;
        $optionalSensorData->voc_air_flow = $data['voc_air_flow'] ?? null;
        $optionalSensorData->voc_det_temp = $data['voc_det_temp'] ?? null;
        $optionalSensorData->voc_oxid_temp = $data['voc_oxid_temp'] ?? null;
        $optionalSensorData->pm2_5_qtotal = $data['pm2_5_qtotal'] ?? null;
        $optionalSensorData->pm2_5_rh = $data['pm2_5_rh'] ?? null;
        $optionalSensorData->pm2_5_at = $data['pm2_5_at'] ?? null;
        $optionalSensorData->pm2_5_maint = $data['pm2_5_maint'] ?? null;
        $optionalSensorData->pm2_5_pwr_fail = $data['pm2_5_pwr_fail'] ?? null;
        $optionalSensorData->pm2_5_ref_memb = $data['pm2_5_ref_memb'] ?? null;
        $optionalSensorData->pm2_5_nz_alarm = $data['pm2_5_nz_alarm'] ?? null;
        $optionalSensorData->pm2_5_flow_alarm = $data['pm2_5_flow_alarm'] ?? null;
        $optionalSensorData->pm2_5_press_alarm = $data['pm2_5_press_alarm'] ?? null;
        $optionalSensorData->pm2_5_span_chk = $data['pm2_5_span_chk'] ?? null;
        $optionalSensorData->pm2_5_cnt_alarm = $data['pm2_5_cnt_alarm'] ?? null;
        $optionalSensorData->pm2_5_tape_alarm = $data['pm2_5_tape_alarm'] ?? null;
        $optionalSensorData->pm10_qtotal = $data['pm10_qtotal'] ?? null;
        $optionalSensorData->pm10_rh = $data['pm10_rh'] ?? null;
        $optionalSensorData->pm10_at = $data['pm10_at'] ?? null;
        $optionalSensorData->pm10_maint = $data['pm10_maint'] ?? null;
        $optionalSensorData->pm10_pwr_fail = $data['pm10_pwr_fail'] ?? null;
        $optionalSensorData->pm10_ref_memb = $data['pm10_ref_memb'] ?? null;
        $optionalSensorData->pm10_nz_alarm = $data['pm10_nz_alarm'] ?? null;
        $optionalSensorData->pm10_flow_alarm = $data['pm10_flow_alarm'] ?? null;
        $optionalSensorData->pm10_press_alarm = $data['pm10_press_alarm'] ?? null;
        $optionalSensorData->pm10_span_chk = $data['pm10_span_chk'] ?? null;
        $optionalSensorData->pm10_cnt_alarm = $data['pm10_cnt_alarm'] ?? null;
        $optionalSensorData->pm10_tape_alarm = $data['pm10_tape_alarm'] ?? null;
        $optionalSensorData->cal_point = $data['cal_point'] ?? null;
        $optionalSensorData->nm = $data['nm'] ?? null;
        $optionalSensorData->nm_det_curr = $data['nm_det_curr'] ?? null;
        $optionalSensorData->so2_flow = $data['so2_flow'] ?? null;
        $optionalSensorData->so2_gas_press = $data['so2_gas_press'] ?? null;
        $optionalSensorData->so2_amb_press = $data['so2_amb_press'] ?? null;
        $optionalSensorData->so2_chass_temp = $data['so2_chass_temp'] ?? null;
        $optionalSensorData->so2_cooler_temp = $data['so2_cooler_temp'] ?? null;
        $optionalSensorData->so2_ref_gain = $data['so2_ref_gain'] ?? null;
        $optionalSensorData->so2_lamp_curr = $data['so2_lamp_curr'] ?? null;

        $optionalSensorData->so2_pmt_hv = $data['so2_pmt_hv'] ?? null;
        $optionalSensorData->so2_ref_volt = $data['so2_ref_volt'] ?? null;
        $optionalSensorData->so2_instr_gain = $data['so2_instr_gain'] ?? null;
        $optionalSensorData->methane_conc = $data['methane_conc'] ?? null;
        $optionalSensorData->methane_det_curr = $data['methane_det_curr'] ?? null;
        $optionalSensorData->thc = $data['thc'] ?? null;
        $optionalSensorData->save();
        return $optionalSensorData;

    }

    public function insert(array $optionalSensorData): void
    {
        $chunks = array_chunk($optionalSensorData, 500);
        foreach ($chunks as $chunk) {
            OptionalSensorData::insert($chunk);
        }
    }
    public function removeDuplicateIdentifiedData($sensorDataIDs = [],$hourlySensorDataIDs = []): void
    {
        if($sensorDataIDs){
            OptionalSensorData::whereIn('sensor_data_id',$sensorDataIDs)->delete();
        }
        if($hourlySensorDataIDs){
            OptionalSensorData::whereIn('hourly_sensor_data_id',$hourlySensorDataIDs)->delete();
        }
    }


}
