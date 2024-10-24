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
        Schema::create('monthly_weather_data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('sensor_location_id');
            $table->string('status',10);

            $table->float('humidity')->nullable();
            $table->float('wind')->nullable();
            $table->float('pressure')->nullable();
            $table->float('temperature')->nullable();
            $table->string('cloud')->nullable();
            $table->float('precipitation')->nullable();
            $table->foreignUuid('monthly_sensor_data_id')->references('id')->on('monthly_sensor_data');
            $table->foreign('sensor_location_id')->references('id')->on('sensor_locations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_weather_data');
    }
};
