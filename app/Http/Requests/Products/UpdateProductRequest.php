<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                             => ['nullable', 'string', 'max:255'],
            'description'                      => ['nullable', 'string', 'max:3000'],
            'price'                            => ['nullable', 'numeric', 'min:0'],
            'is_published'                     => ['nullable', 'boolean'],
            'product_thumbnail'                => ['nullable', 'image', 'max:5120'],
            'product_images'                   => ['nullable', 'array'],
            'product_images.*'                 => ['image', 'max:5120'],
            'tags'                             => ['nullable', 'array'],
            'tags.*'                           => ['string', 'max:100'],
            'categories'                       => ['nullable', 'array'],
            'categories.*'                     => ['string', 'max:100'],
            'variant_groups'                   => ['nullable', 'array'],
            'variant_groups.*.name'            => ['nullable', 'string', 'max:100'],
            'variant_groups.*.index'           => ['nullable', 'integer'],
            'variant_groups.*.variants'        => ['nullable', 'array'],
            'variant_groups.*.variants.*.name' => ['nullable', 'string', 'max:100'],
            'skus'                             => ['nullable', 'array'],
            'skus.*.matrix'                    => ['nullable', 'string'],
            'skus.*.price'                     => ['nullable', 'numeric', 'min:0'],
            'skus.*.total_stock'               => ['nullable', 'integer', 'min:0'],
            'deleted_file_ids'                 => ['nullable', 'array'],
            'deleted_file_ids.*'               => ['string', Rule::exists('files', 'id')],
            'deleted_variant_group_ids'        => ['nullable', 'array'],
            'deleted_variant_group_ids.*'      => ['string', Rule::exists('variant_groups', 'id')],
            'deleted_sku_ids'                  => ['nullable', 'array'],
            'deleted_sku_ids.*'                => ['string', Rule::exists('sku', 'id')],
            'deleted_variant_ids'              => ['nullable', 'array'],
            'deleted_variant_ids.*'            => ['string', Rule::exists('variants', 'id')],
        ];
    }
}
