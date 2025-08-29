<?php

namespace App\Actions\Products;

use App\Models\References\Tag;
use App\Models\Products\Product;
use App\Models\References\Category;
use App\DTOs\Actions\StoreProductDTO;
use App\Services\FileManager\FileManager;
use App\Services\FileManager\DTOs\FileInputDTO;
use App\Services\FileManager\Enums\FileablePrefixes;

class StoreProduct
{
    public function handle(StoreProductDTO $actionDTO): void
    {
        $product = Product::create([
            'name'         => $actionDTO->name,
            'description'  => $actionDTO->description,
            'price'        => $actionDTO->price,
            'is_published' => $actionDTO->is_published,
        ]);

        $product->productStat()->create([
            'total_revenue'       => 0,
            'total_sold_in_units' => 0,
            'last_sold_at'        => null
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

                $product->productTags()->sync($tag->id);
            }
        }

        if ($actionDTO->categories)
        {
            foreach ($actionDTO->categories as $category)
            {
                $category = Category::firstOrCreate([
                    'name' => $category
                ]);

                $product->productCategories()->sync($category->id);
            }
        }

        if ($actionDTO->variant_groups)
        {
            foreach ($actionDTO->variant_groups as $groupDTO)
            {
                $variantGroup = $product->variantGroups()->create([
                    'name'  => $groupDTO->name,
                    'index' => $groupDTO->index
                ]);

                if ($groupDTO->variants)
                {
                    foreach ($groupDTO->variants as $variantDTO)
                    {
                        $variantGroup->variants()->create([
                            'name'  => $variantDTO->name,
                        ]);
                    }
                }
            }
        }

        if ($actionDTO->skus)
        {
            foreach ($actionDTO->skus as $skuDTO)
            {
                $product->skus()->create([
                    'matrix'      => $skuDTO->matrix,
                    'price'       => $skuDTO->price,
                    'total_stock' => $skuDTO->total_stock,
                ]);
            }
        }
    }
}