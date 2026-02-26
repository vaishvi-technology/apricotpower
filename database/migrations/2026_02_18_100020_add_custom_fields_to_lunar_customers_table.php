<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Lunar already has: id, title, first_name, last_name, company_name, vat_no, meta, timestamps
            // Adding our custom fields
            $table->string('email')->unique()->after('last_name');
            $table->string('password')->nullable()->after('email');
            $table->string('phone')->nullable();
            $table->string('tax_id')->nullable();
            $table->boolean('is_tax_exempt')->default(false);
            $table->boolean('net_terms_approved')->default(false);
            $table->decimal('credit_limit', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'password', 'phone', 'tax_id', 'is_tax_exempt',
                'net_terms_approved', 'credit_limit',
                'notes', 'last_login_at',
                'remember_token', 'email_verified_at', 'deleted_at',
            ]);
        });
    }
};
