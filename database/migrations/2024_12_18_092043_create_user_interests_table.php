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
        Schema::create('user_interests', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('property_id');
            $table->decimal('final_rent', 10, 2);
            $table->decimal('deposit', 10, 2);
            $table->decimal('maintenance_per_month', 10, 2);
            $table->bigInteger('owner_id')->nullable(); // Inquiry message
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected', 'closed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interests');
    }
};
