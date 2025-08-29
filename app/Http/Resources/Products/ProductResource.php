<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Request;
use App\Http\Resources\Supports\FileResource;
use App\Http\Resources\References\TagResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\References\CategoryResource;

/**
 * @see \App\Models\Products\Product
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'description'        => $this->description,
            'price'              => $this->price,
            'total_stock'        => $this->total_stock,
            'is_published'       => $this->is_published,
            'product_thumbnails' => $this->whenLoaded((new FileResource($this->productThumbnail))->toArray($request)),
            'product_images'     => $this->whenLoaded(FileResource::collection($this->productImages)->resolve()),
            'product_stat'       => $this->whenLoaded((new ProductStatResource($this->productStat))->toArray($request)),
            'variant_groups'     => $this->whenLoaded(VariantGroupResource::collection($this->variantGroups)->resolve()),
            'product_tags'       => $this->whenLoaded(TagResource::collection($this->productTags)->resolve()),
            'product_categories' => $this->whenLoaded(CategoryResource::collection($this->productCategories)->resolve()),
            'created_at'         => $this->created_at,
        ];
    }
}