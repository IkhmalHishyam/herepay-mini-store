<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'total_stock' => $this->total_stock,
            'is_published' => $this->is_published,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'thumbnail' => $this->whenLoaded('productThumbnail', function () {
                return [
                    'id' => $this->productThumbnail?->id,
                    'file_name' => $this->productThumbnail?->file_name,
                    'file_path' => $this->productThumbnail?->file_path,
                    'file_url' => $this->productThumbnail?->file_url,
                ];
            }),
            
            'images' => $this->whenLoaded('productImages', function () {
                return $this->productImages->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'file_name' => $image->file_name,
                        'file_path' => $image->file_path,
                        'file_url' => $image->file_url,
                    ];
                });
            }),
            
            'categories' => $this->whenLoaded('productCategories', function () {
                return $this->productCategories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                    ];
                });
            }),
            
            'tags' => $this->whenLoaded('productTags', function () {
                return $this->productTags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                    ];
                });
            }),
            
            'variant_groups' => $this->whenLoaded('variantGroups', function () {
                return $this->variantGroups->map(function ($variantGroup) {
                    return [
                        'id' => $variantGroup->id,
                        'name' => $variantGroup->name,
                        'is_active' => $variantGroup->is_active,
                        'variants' => $variantGroup->variants->map(function ($variant) {
                            return [
                                'id' => $variant->id,
                                'name' => $variant->name,
                                'description' => $variant->description,
                                'price' => $variant->price,
                                'total_stock' => $variant->total_stock,
                            ];
                        }),
                    ];
                });
            }),
            
            'has_variants' => $this->whenLoaded('variantGroups', function () {
                return $this->variantGroups->count() > 0;
            }),
            
            'variant_count' => $this->whenLoaded('variantGroups', function () {
                return $this->variantGroups->sum(function ($group) {
                    return $group->variants->count();
                });
            }),
            
            'is_in_stock' => $this->total_stock > 0,
            'stock_status' => $this->total_stock > 10 ? 'in_stock' : ($this->total_stock > 0 ? 'low_stock' : 'out_of_stock'),
        ];
    }
}