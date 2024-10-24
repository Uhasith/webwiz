<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('optional_sensor_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_location_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('sensor_data_id')->nullable()->constrained('sensor_datas');
            $table->foreignUuid('hourly_sensor_data_id')->nullable()->references('id')->on('hourly_sensor_data');

            $table->string('status',10);
            $table->float('at')->nullable();
            $table->float('rh')->nullable();
            $table->float('bp')->nullable();
            $table->float('nc_0_5')->nullable();
            $table->float('nc_1_0')->nullable();
            $table->float('nc_2_5')->nullable();
            $table->float('nc_4_0')->nullable();
            $table->float('nc_10')->nullable();
            $table->float('o3_flow')->nullable();
            $table->float('o3_gas_press')->nullable();
            $table->float('o3_amb_press')->nullable();
            $table->float('o3_chass_temp')->nullable();
            $table->float('o3_lamp_curr')->nullable();
            $table->float('o3_pga_gain')->nullable();
            $table->float('solar_rad')->nullable();
            $table->float('rain_gauge')->nullable();
            $table->float('zero_v_ref')->nullable();
            $table->float('two_five_v_ref')->nullable();
            $table->float('five_v_ref')->nullable();
            $table->float('ws_raw')->nullable();
            $table->float('wd_raw')->nullable();
            $table->float('room_temp')->nullable();
            $table->float('room_rh')->nullable();
            $table->float('ws_average')->nullable();
            $table->float('wd_average')->nullable();
            $table->float('sigma')->nullable();
            $table->float('hto_alarm')->nullable();
            $table->float('co_flow')->nullable();
            $table->float('co_gas_press')->nullable();
            $table->float('co_amb_press')->nullable();
            $table->float('co_cell_temp')->nullable();
            $table->float('co_chass_temp')->nullable();
            $table->float('co_ref_volt')->nullable();
            $table->float('co_input_pot')->nullable();
            $table->float('co_pga_gain')->nullable();
            $table->float('co_cooler_volt')->nullable();
            $table->float('co_bgd_volt')->nullable();
            $table->float('nox')->nullable();
            $table->float('nox_flow')->nullable();
            $table->float('nox_gas_press')->nullable();
            $table->float('no')->nullable();
            $table->float('no_amb_press')->nullable();
            $table->float('no_chass_temp')->nullable();
            $table->float('no_cooler_temp')->nullable();
            $table->float('no_pmt_hv')->nullable();
            $table->float('no_bgd_volt')->nullable();
            $table->float('no_hv_adjust')->nullable();
            $table->float('voc')->nullable();
            $table->float('voc_sample_flow')->nullable();
            $table->float('voc_fuel_flow')->nullable();
            $table->float('voc_air_flow')->nullable();
            $table->float('voc_det_temp')->nullable();
            $table->float('voc_oxid_temp')->nullable();
            $table->float('pm2_5_qtotal')->nullable();
            $table->float('pm2_5_rh')->nullable();
            $table->float('pm2_5_at')->nullable();
            $table->float('pm2_5_maint')->nullable();
            $table->float('pm2_5_pwr_fail')->nullable();
            $table->float('pm2_5_ref_memb')->nullable();
            $table->float('pm2_5_nz_alarm')->nullable();
            $table->float('pm2_5_flow_alarm')->nullable();
            $table->float('pm2_5_press_alarm')->nullable();
            $table->float('pm2_5_span_chk')->nullable();
            $table->float('pm2_5_cnt_alarm')->nullable();
            $table->float('pm2_5_tape_alarm')->nullable();
            $table->float('pm10_qtotal')->nullable();
            $table->float('pm10_rh')->nullable();
            $table->float('pm10_at')->nullable();
            $table->float('pm10_maint')->nullable();
            $table->float('pm10_pwr_fail')->nullable();
            $table->float('pm10_ref_memb')->nullable();
            $table->float('pm10_nz_alarm')->nullable();
            $table->float('pm10_flow_alarm')->nullable();
            $table->float('pm10_press_alarm')->nullable();
            $table->float('pm10_span_chk')->nullable();
            $table->float('pm10_cnt_alarm')->nullable();
            $table->float('pm10_tape_alarm')->nullable();
            $table->float('cal_point')->nullable();
            $table->float('nm')->nullable();
            $table->float('nm_det_curr')->nullable();
            $table->float('so2_flow')->nullable();
            $table->float('so2_gas_press')->nullable();
            $table->float('so2_amb_press')->nullable();
            $table->float('so2_chass_temp')->nullable();
            $table->float('so2_cooler_temp')->nullable();
            $table->float('so2_ref_gain')->nullable();
            $table->float('so2_lamp_curr')->nullable();
            $table->float('so2_pmt_hv')->nullable();
            $table->float('so2_ref_volt')->nullable();
            $table->float('so2_instr_gain')->nullable();
            $table->float('methane_conc')->nullable();
            $table->float('methane_det_curr')->nullable();
            $table->float('thc')->nullable();
            $table->float('ch4')->nullable();
            $table->float('tvoc')->nullable();
            $table->string('type')->nullable();
            $table->string('identifier')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optional_sensor_data');
    }
};
