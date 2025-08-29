<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                             => ['required', 'string', 'max:255'],
            'description'                      => ['nullable', 'string', 'max:3000'],
            'price'                            => ['required', 'numeric', 'min:0'],
            'is_published'                     => ['required', 'boolean'],
            'product_thumbnail'                => ['nullable', 'image', 'max:5120'],
            'product_images'                   => ['nullable', 'array'],
            'product_images.*'                 => ['image', 'max:5120'],
            'tags'                             => ['nullable', 'array'],
            'tags.*'                           => ['string', 'max:100'],
            'categories'                       => ['nullable', 'array'],
            'categories.*'                     => ['string', 'max:100'],
            'variant_groups'                   => ['nullable', 'array'],
            'variant_groups.*.name'            => ['required', 'string', 'max:100'],
            'variant_groups.*.index'           => ['required', 'integer'],
            'variant_groups.*.variants'        => ['nullable', 'array'],
            'variant_groups.*.variants.*.name' => ['required', 'string', 'max:100'],
            'skus'                             => ['nullable', 'array'],
            'skus.*.matrix'                    => ['required', 'string'],
            'skus.*.price'                     => ['required', 'numeric', 'min:0'],
            'skus.*.total_stock'               => ['required', 'integer', 'min:0'],
        ];
    }
}
