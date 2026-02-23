<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * No legacy equivalent - this is a NEW table for migration tracking.
     * Maps old .NET database IDs to new Laravel auto-increment IDs.
     * Used during data migration to resolve foreign key references.
     *
     * Example usage:
     *   legacy_table='accounts', legacy_id=12345, new_table='customers', new_id=1
     */
    public function up(): void
    {
        Schema::create('legacy_id_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('legacy_table');     // e.g., 'accounts', 'items', 'carts'
            $table->unsignedBigInteger('legacy_id');
            $table->string('new_table');        // e.g., 'customers', 'products', 'orders'
            $table->unsignedBigInteger('new_id');
            $table->timestamps();

            $table->unique(['legacy_table', 'legacy_id']);
            $table->index(['new_table', 'new_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_id_mappings');
    }
};
