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
        Schema::create('shipment_notes', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');

            // Note content
            $table->longText('note_text')->nullable();
            $table->datetime('note_datetime');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('shipment_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_notes');
    }
};
