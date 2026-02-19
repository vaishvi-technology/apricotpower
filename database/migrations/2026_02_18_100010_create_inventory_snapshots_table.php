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
        Schema::create('inventory_snapshots', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign key to items
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');

            // Snapshot data
            $table->decimal('value', 18, 2);
            $table->date('snapshot_date');
            $table->integer('inventory');
            $table->boolean('is_arrived')->default(false);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_snapshots');
    }
};
