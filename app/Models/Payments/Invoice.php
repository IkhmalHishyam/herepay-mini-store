<?php

namespace App\Models\Payments;

use App\Enums\InvoiceStatusEnum;
use App\Models\User;
use App\Models\Orders\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'amount',
        'status',
        'paid_at',

        // Foreign Keys
        'order_id',
        'user_id'
    ];

    protected $casts = [
        'invoice_number' => 'string',
        'amount'         => 'float',
        'status'         => InvoiceStatusEnum::class,
        'paid_at'        => 'datetime',
        'order_id'       => 'string',
        'user_id'        => 'string',
    ];

    //******************************************************************************************************************
    // RELATIONSHIPS
    //******************************************************************************************************************

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
