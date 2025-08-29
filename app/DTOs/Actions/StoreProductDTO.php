<?php

namespace App\DTOs\Actions;

use App\DTOs\Actions\Subs\SkuDTO;
use Illuminate\Http\UploadedFile;
use App\DTOs\Actions\Subs\VariantGroupDTO;

class StoreProductDTO
{
    public string $name;

    public ?string $description;

    public float $price;

    public bool $is_published;

    public ?UploadedFile $product_thumbnail;

    public ?array $product_images;

    public ?array $tags;

    public ?array $categories;

    public array $variant_groups;

    public array $skus;

    public function __construct(array $data)
    {
        $this->name              = $data['name'];
        $this->description       = $data['description'] ?? null;
        $this->price             = $data['price'];
        $this->is_published      = $data['is_published'];
        $this->product_thumbnail = $data['product_thumbnail'] ?? null;
        $this->product_images    = $data['product_images']    ?? null;
        $this->tags              = $data['tags']              ?? null;
        $this->categories        = $data['categories']        ?? null;
        $this->variant_groups    = array_map(fn($group) => new VariantGroupDTO($group), $data['variant_groups'] ?? []);
        $this->skus              = array_map(fn($sku) => new SkuDTO($sku), $data['skus'] ?? []);
    }
}