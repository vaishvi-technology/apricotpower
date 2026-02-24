<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Lunar\Models\Cart;
use Lunar\Models\Customer as LunarCustomer;
use Lunar\Models\Order;
use App\Notifications\CustomerResetPasswordNotification;

/**
 * Extends Lunar's Customer model with legacy Apricot Power fields
 * and direct authentication support via Sanctum.
 *
 * Lunar base fields: id, title, first_name, last_name, company_name, vat_no, meta, timestamps
 * Added fields: email, password, phone, is_tax_exempt, net_terms_approved, credit_limit,
 *               current_balance, is_active, is_vip, is_retailer, notes, etc.
 *
 * Lunar's default architecture: User (auth) -> customer_user pivot -> Customer (data)
 * Our extension adds: Customer can also authenticate directly via Sanctum API tokens.
 *
 * Registered via ModelManifest::replace() in AppServiceProvider.
 */
class Customer extends LunarCustomer implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, HasApiTokens, Notifiable, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'attribute_data' => \Lunar\Base\Casts\AsAttributeData::class,
        'meta' => \Illuminate\Database\Eloquent\Casts\AsArrayObject::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_tax_exempt' => 'boolean',
        'net_terms_approved' => 'boolean',
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'account_locked' => 'boolean',
        'subscribe_to_list' => 'boolean',
        'is_vip' => 'boolean',
        'vip_since' => 'date',
        'vip_expire' => 'date',
        'is_retailer' => 'boolean',
        'is_online_retailer' => 'boolean',
        'last_login_at' => 'datetime',
        'last_order_at' => 'datetime',
        'agreed_terms_at' => 'datetime',
    ];

    /**
     * Full name accessor.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Carts belonging to this customer.
     * Required by Lunar's CartSessionManager when Customer is the authenticated model.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::modelClass());
    }

    /**
     * Orders belonging to this customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::modelClass());
    }

    /**
     * Default shipping address.
     */
    public function defaultShippingAddress(): HasOne
    {
        return $this->hasOne(\Lunar\Models\Address::modelClass())
            ->where('shipping_default', true);
    }

    /**
     * Default billing address.
     */
    public function defaultBillingAddress(): HasOne
    {
        return $this->hasOne(\Lunar\Models\Address::modelClass())
            ->where('billing_default', true);
    }

    /**
     * Wholesale stores for this customer.
     */
    public function stores(): HasMany
    {
        return $this->hasMany(CustomerStore::class);
    }

    /**
     * Dealer record for this customer.
     */
    public function dealer(): HasOne
    {
        return $this->hasOne(Dealer::class);
    }

    /**
     * Retailer profile for this customer.
     */
    public function retailerProfile(): HasOne
    {
        return $this->hasOne(RetailerProfile::class);
    }

    /**
     * Send the password reset notification to the customer's storefront reset URL.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomerResetPasswordNotification($token));
    }
}
