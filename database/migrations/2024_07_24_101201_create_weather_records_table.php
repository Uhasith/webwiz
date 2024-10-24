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
        Schema::create('weather_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('sensor_location_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('sensor_data_id')->constrained('sensor_datas');
            $table->string('status',10);
            $table->float('humidity')->nullable();
            $table->float('wind')->nullable();
            $table->float('pressure')->nullable();
            $table->float('temperature')->nullable();
            $table->string('cloud')->nullable();
            $table->float('precipitation')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_records');
    }
};
