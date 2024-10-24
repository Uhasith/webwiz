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
        Schema::create('weekly_sensor_data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('sensor_location_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status',10);
            
            $table->float('pm2_5')->nullable()->comment('PM2.5 level');
            $table->float('pm10')->nullable()->comment('PM10 level');
            $table->float('o3')->nullable()->comment('Ozone level');
            $table->float('co')->nullable()->comment('Carbon Monoxide level');
            $table->float('no2')->nullable()->comment('Nitrogen Dioxide level');
            $table->float('so2')->nullable()->comment('Sulfur Dioxide level');
            $table->float('co2')->nullable()->comment('Carbon Dioxide level');    
            $table->float('pm1')->nullable()->comment('PM1 level');
            $table->float('pm4')->nullable()->comment('PM4 level');
            $table->float('pm100')->nullable()->comment('PM100 level');

            $table->json('pm2_5_aqi')->nullable()->comment('PM2.5 AQI data');
            $table->json('pm10_aqi')->nullable()->comment('PM10 AQI data');
            $table->json('o3_aqi')->nullable()->comment('Ozone AQI data');
            $table->json('co_aqi')->nullable()->comment('Carbon Monoxide AQI data');
            $table->json('no2_aqi')->nullable()->comment('Nitrogen Dioxide AQI data');
            $table->json('so2_aqi')->nullable()->comment('Sulfur Dioxide AQI data');
            $table->json('co2_aqi')->nullable()->comment('Carbon Dioxide AQI data');
            $table->json('aqi')->nullable();

            $table->string('pm2_5_status',10)->nullable()->comment('PM2.5 status');
            $table->string('pm10_status',10)->nullable()->comment('PM10 status');
            $table->string('o3_status',10)->nullable()->comment('Ozone status');
            $table->string('co_status',10)->nullable()->comment('Carbon Monoxide status');
            $table->string('no2_status',10)->nullable()->comment('Nitrogen Dioxide status');
            $table->string('so2_status',10)->nullable()->comment('Sulfur Dioxide status');
            $table->string('co2_status',10)->nullable()->comment('Carbon Dioxide status');
            $table->string('pm1_status',10)->nullable()->comment('PM1 status');
            $table->string('pm4_status',10)->nullable()->comment('PM4 status');
            $table->string('pm100_status',10)->nullable()->comment('PM100 status');   
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
        Schema::dropIfExists('weekly_sensor_data');
    }
};
