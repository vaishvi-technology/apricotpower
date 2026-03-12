<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['is_tax_exempt', 'store_date', 'store_count']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_tax_exempt')->default(false);
            $table->date('store_date')->nullable();
            $table->integer('store_count')->default(1);
        });
    }
};
