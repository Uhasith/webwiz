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
        Schema::create('external_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('api_key');
            $table->json('whitelisted_ips')->nullable();
            $table->json('locations')->nullable();
            $table->json('sensors')->nullable();
            $table->boolean('recent_data')->default(false);
            $table->boolean('hourly_data')->default(false);
            $table->boolean('daily_data')->default(false);
            $table->boolean('monthly_data')->default(false);
            $table->boolean('annually_data')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_accesses');
    }
};
