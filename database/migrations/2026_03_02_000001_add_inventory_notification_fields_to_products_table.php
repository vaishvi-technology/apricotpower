<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('notify_at')->default(0)->after('quantity_size');
            $table->timestamp('low_stock_notified_at')->nullable()->after('notify_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['notify_at', 'low_stock_notified_at']);
        });
    }
};
