<?php

namespace App\Actions\Products;

use App\Models\Products\Sku;
use App\Models\References\Tag;
use App\Models\Products\Product;
use App\Models\Products\Variant;
use App\Models\References\Category;
use App\Models\Products\VariantGroup;
use App\DTOs\Actions\UpdateProductDTO;
use App\Services\FileManager\FileManager;
use App\Services\FileManager\DTOs\FileInputDTO;
use App\Services\FileManager\Enums\FileablePrefixes;

class UpdateProduct
{
    public function handle(UpdateProductDTO $actionDTO, string $product_id): Product
    {
        $product = Product::findOrFail($product_id);

        $product->update([
            'name'         => $actionDTO->name         ?? $product->name,
            'description'  => $actionDTO->description  ?? $product->description,
            'price'        => $actionDTO->price        ?? $product->price,
            'is_published' => $actionDTO->is_published ?? $product->is_published,
        ]);

        if ($actionDTO->product_thumbnail) 
        {
            $fileManager = new FileManager;
            $fileManager->store(new FileInputDTO(
                user        : null,
                model       : $product,
                files       : $actionDTO->product_thumbnail,
                fileable    : FileablePrefixes::PRODUCT_THUMBNAIL,
                is_singular : true
            ));
        }

        if ($actionDTO->product_images) 
        {
            $fileManager = new FileManager;
            $fileManager->store(new FileInputDTO(
                user        : null,
                model       : $product,
                files       : $actionDTO->product_images,
                fileable    : FileablePrefixes::PRODUCT_IMAGE,
                is_singular : false
            ));
        }

        if ($actionDTO->tags)
        {
            foreach ($actionDTO->tags as $tag)
            {
                $tag = Tag::firstOrCreate([
                    'name' => $tag
                ]);

                $product->productTags()->sync($tag->id, false);
            }
        }

        if ($actionDTO->categories)
        {
            foreach ($actionDTO->categories as $category)
            {
                $category = Category::firstOrCreate([
                    'name' => $category
                ]);

                $product->productCategories()->sync($category->id, false);
            }
        }

        if ($actionDTO->variant_groups)
        {
            foreach ($actionDTO->variant_groups as $groupDTO)
            {
                $variantGroup = VariantGroup::updateOrCreate([
                    'name'       => $groupDTO->name,
                    'product_id' => $product->id
                ], [
                    'index' => $groupDTO->index
                ]);

                if ($groupDTO->variants)
                {
                    foreach ($groupDTO->variants as $variantDTO)
                    {
                        Variant::updateOrCreate([
                            'name'             => $variantDTO->name,
                            'variant_group_id' => $variantGroup->id
                        ]);
                    }
                }
            }
        }

        if ($actionDTO->skus)
        {
            foreach ($actionDTO->skus as $skuDTO)
            {
                Sku::updateOrCreate([
                    'matrix'     => $skuDTO->matrix,
                    'product_id' => $product->id
                ], [
                    'price'       => $skuDTO->price,
                    'total_stock' => $skuDTO->total_stock,
                ]);
            }
        }

        if ($actionDTO->deleted_file_ids)
        {
            $fileManager = new FileManager;
            $fileManager->bulkDelete($actionDTO->deleted_file_ids);
        }

        if ($actionDTO->deleted_variant_group_ids)
        {
            VariantGroup::whereIn('id', $actionDTO->deleted_variant_group_ids)->forceDelete();
        }

        if ($actionDTO->deleted_sku_ids)
        {
            Sku::whereIn('id', $actionDTO->deleted_sku_ids)->forceDelete();
        }

        if ($actionDTO->deleted_variant_ids)
        {
            Variant::whereIn('id', $actionDTO->deleted_variant_ids)->forceDelete();
        }

        return $product;
    }
}