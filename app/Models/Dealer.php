<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'business_name',
        'slug',
        'description',
        'website',
        'email',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'hours_of_operation',
        'logo',
        'is_featured',
        'show_on_locator',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'hours_of_operation' => 'array',
        'is_featured' => 'boolean',
        'show_on_locator' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
