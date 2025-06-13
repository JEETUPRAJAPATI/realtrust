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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->string('unique_id');
            $table->string('phone_number');
            $table->string('template_name');
            // $table->json('variables')->nullable();
            $table->text('variables')->nullable();
            $table->string('message_id')->nullable();
            $table->string('status')->default('pending');
            // $table->json('api_response')->nullable();
            $table->text('api_response')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
