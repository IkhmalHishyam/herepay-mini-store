<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_city',
        'shipping_state',
        'shipping_postcode',
        'shipping_country',
        'order_notes',

        // Foreign Keys
        'order_id'
    ];

    protected $casts = [
        'customer_name'      => 'string',
        'customer_email'     => 'string',
        'customer_phone'     => 'string',
        'shipping_address_1' => 'string',
        'shipping_address_2' => 'string',
        'shipping_city'      => 'string',
        'shipping_state'     => 'string',
        'shipping_postcode'  => 'string',
        'shipping_country'   => 'string',
        'order_notes'        => 'string',
        'order_id'           => 'string',
    ];

    //******************************************************************************************************************
    // RELATIONSHIPS
    //******************************************************************************************************************

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
