<?php

namespace App\Models\Products;

use App\Models\Carts\Cart;
use App\Models\Supports\File;
use App\Models\References\Tag;
use App\Models\References\Category;
use App\Scopes\ProductScope;
use App\Services\FileManager\Enums\FileablePrefixes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use ProductScope;
    use HasUlids, SoftDeletes;
    
    protected $fillable = [
        'name',
        'description',
        'price',
        'is_published',
    ];

    protected $casts = [
        'name'         => 'string',
        'description'  => 'string',
        'price'        => 'float',
        'is_published' => 'boolean',
    ];

    // *****************************************************************************************************************
    // RELATIONSHIPS
    // *****************************************************************************************************************

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }
    
    public function productThumbnail(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable')
            ->where('fileable', FileablePrefixes::PRODUCT_THUMBNAIL->value);
    }

    public function productImages(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable')
            ->where('fileable', FileablePrefixes::PRODUCT_IMAGE->value);
    }

    public function variantGroups(): HasMany
    {
        return $this->hasMany(VariantGroup::class);
    }

    public function productTags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags', 'product_id', 'tag_id');
    }

    public function productCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    public function productStat(): HasOne
    {
        return $this->hasOne(ProductStat::class);
    }

    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class);
    }

    public function cartProducts(): BelongsToMany
    {
        return $this->belongsToMany(Cart::class, 'cart_products', 'product_id', 'cart_id')
            ->withPivot(['name', 'sku', 'price_per_unit', 'quantity', 'total_price'])
            ->withTimestamps();
    }
}
