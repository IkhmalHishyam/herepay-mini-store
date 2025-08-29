<?php

namespace App\Models\Orders;

use App\Models\User;
use App\Enums\OrderStatusEnum;
use App\Models\Payments\Invoice;
use App\Scopes\OrderScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use OrderScope;
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'order_number',
        'total_price',
        'status',

        // Foreign Keys
        'user_id'
    ];

    protected $casts = [
        'order_number' => 'string',
        'total_price'  => 'float',
        'user_id'      => 'string',
        'status'       => OrderStatusEnum::class,
    ];

    //******************************************************************************************************************
    // RELATIONSHIPS
    //******************************************************************************************************************

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetail(): HasOne
    {
        return $this->hasOne(OrderDetail::class);
    }

    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
