<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @see \App\Models\Products\VariantGroup
 */
class VariantGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'is_active' => $this->is_active,
            'variants'  => $this->whenLoaded(VariantResource::collection($this->variants)->resolve()),
        ];
    }
}