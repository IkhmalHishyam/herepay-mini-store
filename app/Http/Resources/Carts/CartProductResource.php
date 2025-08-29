<?php

namespace App\Http\Resources\Carts;

use Illuminate\Http\Request;
use App\Http\Resources\Supports\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartProductResource extends JsonResource
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
            'price_per_unit'     => $this->pivot->price_per_unit,
            'quantity'           => $this->pivot->quantity,
            'total_price'        => $this->pivot->total_price,
        ];
    }
}
