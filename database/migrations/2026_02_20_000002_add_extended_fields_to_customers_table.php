<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('email')->unique()->after('company_name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('password')->after('email_verified_at');
            $table->string('phone')->nullable()->after('password');
            $table->boolean('is_tax_exempt')->default(false)->after('tax_identifier');
            $table->string('tax_exempt_certificate')->nullable()->after('is_tax_exempt');
            $table->boolean('net_terms_approved')->default(false)->after('tax_exempt_certificate');
            $table->decimal('credit_limit', 10, 2)->nullable()->after('net_terms_approved');
            $table->decimal('current_balance', 10, 2)->default(0)->after('credit_limit');
            $table->text('notes')->nullable()->after('current_balance');
            $table->boolean('is_active')->default(true)->after('notes');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->rememberToken();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('company_name');
            $table->index(['last_name', 'first_name']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['company_name']);
            $table->dropIndex(['last_name', 'first_name']);
            $table->dropColumn([
                'email', 'email_verified_at', 'password', 'phone',
                'is_tax_exempt', 'tax_exempt_certificate', 'net_terms_approved',
                'credit_limit', 'current_balance', 'notes', 'is_active',
                'last_login_at', 'remember_token', 'deleted_at'
            ]);
        });
    }
};
