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
        Schema::create('schedule_notification', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('sender_id'); 
            $table->json('receiver_id');
            $table->string('type');
            $table->string('title'); 
            $table->text('description')->nullable();
            $table->text('status');
            $table->dateTime('scheduled_at'); 
            $table->timestamps(); 

            $table->foreign('sender_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_notification');
    }
};
