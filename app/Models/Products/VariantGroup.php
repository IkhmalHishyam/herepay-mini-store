<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantGroup extends Model
{
    use HasUlids, SoftDeletes;
    
    protected $fillable = [
        'name',
        'index',

        // Foreign Keys
        'product_id',
    ];

    protected $casts = [
        'name'       => 'string',
        'index'      => 'integer',
        'product_id' => 'string',
    ];

    // *****************************************************************************************************************
    // RELATIONSHIPS
    // *****************************************************************************************************************

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }
}
