<?php

namespace App\Models\Orders;

use App\Enums\FileableEnum;
use App\Models\Supports\File;
use App\Services\FileManager\Enums\FileablePrefixes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class OrderProduct extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price_per_unit',
        'quantity',
        'total_price',

        // Foreign Keys
        'order_id'
    ];

    protected $casts = [
        'name'           => 'string',
        'sku'            => 'string',
        'description'    => 'string',
        'price_per_unit' => 'float',
        'quantity'       => 'integer',
        'total_price'    => 'float',
        'order_id'       => 'string',
    ];

    //******************************************************************************************************************
    // RELATIONSHIPS
    //******************************************************************************************************************

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function productImages(): MorphToMany
    {
        return $this->morphToMany(File::class, 'snapshotable', 'file_snapshots')
            ->where('fileable', FileablePrefixes::PRODUCT_IMAGE->value);
    }
}
