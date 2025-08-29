<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStat extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'total_revenue',
        'total_sold_in_units',
        'last_sold_at',
        'product_id',
    ];

    protected $casts = [
        'total_revenue'       => 'float',
        'total_sold_in_units' => 'integer',
        'last_sold_at'        => 'datetime',
        'product_id'          => 'string',
    ];

    //******************************************************************************************************************
    // RELATIONSHIPS
    //******************************************************************************************************************

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
