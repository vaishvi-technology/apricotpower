<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authorize_net_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('profile_id')->unique();  // CIM customerProfileId
            $table->string('merchant_customer_id')->nullable();
            $table->string('email')->nullable();
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->cascadeOnDelete();

            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authorize_net_profiles');
    }
};
