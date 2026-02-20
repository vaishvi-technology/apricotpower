<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legacy_id_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 50); // customer, product, order, category, etc.
            $table->unsignedBigInteger('legacy_id');
            $table->unsignedBigInteger('new_id');
            $table->json('legacy_data')->nullable();
            $table->timestamps();

            $table->unique(['entity_type', 'legacy_id']);
            $table->index(['entity_type', 'new_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legacy_id_mappings');
    }
};
