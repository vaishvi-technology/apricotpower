<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Maps from legacy `dealer` table.
     * Legacy field mappings:
     *   CustomerID  -> customer_id (optional FK to customers)
     *   Name        -> business_name
     *   Street      -> address_line_1
     *   City        -> city
     *   State       -> state
     *   PostalCode  -> postal_code
     *   Country     -> country
     *   Phone       -> phone
     *   WebSite     -> website
     *   Email       -> email
     *   DealerLat   -> latitude
     *   DealerLon   -> longitude
     *   Hidden      -> is_active (inverted)
     *   RankInList   -> sort_order (via is_featured)
     *   ProductsSold -> description
     */
    public function up(): void
    {
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('business_name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->string('country')->default('US');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('hours_of_operation')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('show_on_locator')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('customer_id');
            $table->index('is_active');
            $table->index('show_on_locator');
            $table->index(['latitude', 'longitude']);
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealers');
    }
};
