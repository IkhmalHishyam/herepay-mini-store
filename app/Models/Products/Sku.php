<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sku extends Model
{
    use HasUlids, SoftDeletes;

    protected $table = 'sku';

    protected $fillable = [
        'matrix',
        'price',
        'total_stock',

        // Foreign Keys
        'product_id'
    ];

    protected $casts = [
        'matrix'      => 'string',
        'price'       => 'float',
        'total_stock' => 'integer',
        'product_id'  => 'string'
    ];

    //******************************************************************************************************************
    // RELATIONSHIPS
    //******************************************************************************************************************

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
