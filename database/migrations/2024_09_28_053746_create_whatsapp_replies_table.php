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
        Schema::create('whatsapp_replies', function (Blueprint $table) {
            $table->id();
            $table->string('from'); // Sender's phone number
            $table->text('message'); // The message content
            $table->string('message_id')->nullable(); // WhatsApp message ID
            $table->string('status')->default('received'); // Status of the message
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_replies');
    }
};
