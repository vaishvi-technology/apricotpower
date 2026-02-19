<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Lunar\Base\Traits\LunarUser;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, LunarUser, Notifiable;

    /**
     * Storefront customer account columns.
     * Mapped from legacy `accounts` table:
     *   accounts.ShippingNameFirst → first_name
     *   accounts.ShippingNameLast  → last_name
     *   accounts.Email             → email
     *   accounts.Phone             → phone
     *   accounts.Password          → password (re-hashed with bcrypt)
     *   accounts.Company           → company_name
     *
     * Admin/staff columns live in `lunar_staff` (managed via Filament).
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'company_name',
        'email',
        'password',
        'phone',
        'email_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_preferences' => 'array',
    ];
}
