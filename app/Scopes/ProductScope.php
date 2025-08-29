<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait ProductScope
{
    public function scopeSearchable(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function (Builder $query) use ($search) 
        {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%")
                      ->orWhereHas('productCategories', function ($query) use ($search) {
                          $query->where('name', 'LIKE', "%{$search}%");
                      });
            });
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeByCategories(Builder $query, array $categoryIds): Builder
    {
        return $query->when(!empty($categoryIds), function (Builder $query) use ($categoryIds) {
            $query->whereHas('productCategories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            });
        });
    }

    public function scopeByTags(Builder $query, array $tagIds): Builder
    {
        return $query->when(!empty($tagIds), function (Builder $query) use ($tagIds) {
            $query->whereHas('productTags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            });
        });
    }

    public function scopePriceRange(Builder $query, ?float $minPrice, ?float $maxPrice): Builder
    {
        return $query->when($minPrice !== null, function (Builder $query) use ($minPrice) {
            $query->where('price', '>=', $minPrice);
        })->when($maxPrice !== null, function (Builder $query) use ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        });
    }
}