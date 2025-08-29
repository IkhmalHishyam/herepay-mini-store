<?php

namespace App\Models\Customers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerStat extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'total_orders',
        'total_spent',
        'average_order_value',
        'last_order_date',

        // Foreign Keys
        'user_id',
    ];

    protected $casts = [
        'total_orders'        => 'integer',
        'total_spent'         => 'float',
        'average_order_value' => 'float',
        'last_order_date'     => 'datetime',
        'user_id'             => 'string',
    ];

    //******************************************************************************************************************
    // RELATIONSHIPS
    //******************************************************************************************************************

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
