<?php

namespace App\Models\Payments;

use App\Enums\TransactionStatusEnum;
use App\Models\Orders\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'transaction_no',
        'amount',
        'currency',
        'payment_method',
        'status',
        'paid_at',

        // Foreign Keys
        'invoice_id'
    ];

    protected $casts = [
        'transaction_no' => 'string',
        'amount'         => 'float',
        'currency'       => 'string',
        'payment_method' => 'string',
        'status'         => TransactionStatusEnum::class,
        'paid_at'        => 'datetime',
        'invoice_id'     => 'string',
    ];

    //******************************************************************************************************************
    // RELATIONSHIPS
    //******************************************************************************************************************

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'invoice_id', 'id');
    }
}
