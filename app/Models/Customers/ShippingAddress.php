<?php

namespace App\Models\Customers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingAddress extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address_1',
        'address_2',
        'city',
        'state',
        'postcode',
        'country',
        'is_default',

        // Foreign Keys
        'user_id',
    ];

    protected $casts = [
        'name'       => 'string',
        'email'      => 'string',
        'phone'      => 'string',
        'address_1'  => 'string',
        'address_2'  => 'string',
        'city'       => 'string',
        'state'      => 'string',
        'postcode'   => 'string',
        'country'    => 'string',
        'is_default' => 'boolean',
        'user_id'    => 'string',
    ];

    // *****************************************************************************************************************
    // RELATIONSHIPS
    // *****************************************************************************************************************

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
