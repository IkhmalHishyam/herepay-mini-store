<?php

namespace App\Models\Carts;

use App\Models\Products\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cart extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'total_price',

        // Foreign Keys
        'user_id',
    ];

    protected $casts = [
        'total_price' => 'float',
        'user_id'     => 'string',
    ];

    //******************************************************************************************************************
    // RELATIONSHIPS
    //******************************************************************************************************************

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cartProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'cart_products', 'cart_id', 'product_id')
            ->withPivot(['name', 'sku', 'price_per_unit', 'quantity', 'total_price'])
            ->withTimestamps();
    }
}
