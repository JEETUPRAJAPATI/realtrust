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
            $table->unsignedBigInteger('owner_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('property_type', ['residential', 'commercial']);
            $table->enum('looking_at', ['Rent', 'Sell', 'PG/CO-living']);
            $table->string('city');
            $table->enum('property_detail_type', ['Apartment', 'Villa', 'Independent Floor', 'Independent House']);
            $table->string('building_project_society');
            $table->string('locality');
            $table->string('bhk');
            $table->integer('area')->unsigned();
            $table->enum('furnish_type', ['Fully Furnished', 'Semi Furnished', 'Unfurnished']);
            $table->decimal('rent', 10, 2);
            $table->date('available_for');
            $table->enum('security_deposit', ['None', '1 month', '2 months', 'Custom'])->nullable();
            $table->text('photos')->nullable(); // You can store URLs or file paths as JSON
            $table->string('video')->nullable();
            $table->string('map_location')->nullable();
            $table->boolean('featured')->default(false);
            $table->text('review')->nullable();
            $table->integer('age_property')->unsigned()->nullable();
            $table->integer('bathroom')->unsigned()->default(0);
            $table->integer('balcony')->unsigned()->default(0);
            $table->boolean('covered_parking')->default(false);
            $table->boolean('open_parking')->default(false);
            $table->boolean('zero_brokerage')->default(false);
            $table->boolean('fifteen_days_rent_off')->default(false);
            $table->boolean('one_month_rent_off')->default(false);
            $table->enum('preferred_tenant_type', ['Family', 'Bachelors', 'Company'])->nullable();
            $table->enum('maintenance_charges', ['Include in rent', 'Separate'])->nullable();
            $table->decimal('maintenance_charges_amount', 10, 2)->nullable();
            $table->enum('parking_charges', ['Include in rent', 'Separate'])->nullable();
            $table->decimal('parking_charges_amount', 10, 2)->nullable();
            $table->text('property_description')->nullable();
            $table->json('flat_furnishings')->nullable(); // Store as JSON
            $table->json('society_amenities')->nullable(); // Store as JSON
            // Foreign key constraint
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
