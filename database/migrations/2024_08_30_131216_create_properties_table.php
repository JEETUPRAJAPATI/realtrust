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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->unique();
            $table->string('title', 191);
            $table->string('slug', 191)->unique();
            $table->enum('property_type', ['residential', 'commercial']);
            $table->enum('looking_at', ['Rent', 'Sell', 'PG/CO-living']);
            $table->string('city', 191);
            $table->enum('property_detail_type', ['Apartment', 'Villa', 'Independent Floor', 'Independent House']);
            $table->string('building_project_society', 191);
            $table->string('locality', 191);
            $table->string('bhk', 191);
            $table->unsignedInteger('area');
            $table->enum('furnish_type', ['Fully Furnished', 'Semi Furnished', 'Unfurnished']);
            $table->decimal('rent', 10, 2);
            $table->date('available_for');
            $table->enum('security_deposit', ['None', '1 month', '2 months', 'Custom'])->nullable();
            $table->text('photos')->nullable();
            $table->string('video', 250)->nullable();
            $table->string('map_location', 250)->nullable();
            $table->boolean('featured')->default(false);
            $table->text('review')->nullable();
            $table->unsignedInteger('bathroom')->nullable();
            $table->unsignedInteger('balcony')->nullable();
            $table->unsignedInteger('covered_parking')->nullable();
            $table->unsignedInteger('open_parking')->nullable();
            $table->text('rentals')->nullable();
            $table->enum('preferred_tenant_type', ['Family', 'Bachelors', 'Company'])->nullable();
            $table->text('maintenance_charges')->nullable();
            $table->text('parking_charges')->nullable();
            $table->text('property_description')->nullable();
            $table->text('flat_furnishings')->nullable();
            $table->text('society_amenities')->nullable();
            // Foreign Key Constraints
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
