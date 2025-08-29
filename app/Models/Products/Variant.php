<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Variant extends Model
{
    use HasUlids, SoftDeletes;
    
    protected $fillable = [
        'name',

        // Foreign Keys
        'variant_group_id',
    ];

    protected $casts = [
        'name'             => 'string',
        'variant_group_id' => 'string',
    ];

    // *****************************************************************************************************************
    // RELATIONSHIPS
    // *****************************************************************************************************************

    public function variantGroup(): BelongsTo
    {
        return $this->belongsTo(VariantGroup::class);
    }
}
