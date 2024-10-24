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
        Schema::create('sensor_organization', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('sensor_id');
            $table->unsignedBigInteger('organization_id');
            $table->timestamps();

            $table->foreign('sensor_id')->references('id')->on('sensors');
            $table->foreign('organization_id')->references('id')->on('organization');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_organization');
    }
};
