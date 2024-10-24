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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_template_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('sensor_location_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('sensor_data_id')->constrained('sensor_datas');
            $table->foreignId('equipment_type_id')->nullable()->constrained()->onDelete('set null');
            $table->string('parameters');
            $table->string('status',10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
