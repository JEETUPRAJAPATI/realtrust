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
        Schema::create('conform_timing', function (Blueprint $table) {
            $table->id();
            $table->string('property_id');
            $table->unsignedBigInteger('field_manager_id')->nullable();
            $table->timestamp('timing');
            $table->integer('conform_timing')->default('0')->comment('1 => conform time field manager, 0 => not conform time field manager');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conform_timing');
    }
};
