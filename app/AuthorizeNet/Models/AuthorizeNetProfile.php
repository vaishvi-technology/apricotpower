<?php

namespace App\AuthorizeNet\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorizeNetProfile extends Model
{
    protected $fillable = [
        'customer_id',
        'profile_id',
        'merchant_customer_id',
        'email',
        'description',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
