<?php

namespace App\DTOs\Actions;

use App\DTOs\Actions\Subs\SkuDTO;
use Illuminate\Http\UploadedFile;
use App\DTOs\Actions\Subs\VariantGroupDTO;

class UpdateProductDTO
{
    public string $name;

    public ?string $description;

    public ?float $price;

    public ?bool $is_published;

    public ?UploadedFile $product_thumbnail;

    public ?array $product_images;

    public ?array $tags;

    public ?array $categories;

    public array $variant_groups;

    public array $skus;

    public ?array $deleted_file_ids;

    public ?array $deleted_variant_group_ids;

    public ?array $deleted_sku_ids;

    public ?array $deleted_variant_ids;

    public function __construct(array $data)
    {
        $this->name                      = $data['name']              ?? null;
        $this->description               = $data['description']       ?? null;
        $this->price                     = $data['price']             ?? null;
        $this->is_published              = $data['is_published']      ?? null;
        $this->product_thumbnail         = $data['product_thumbnail'] ?? null;
        $this->product_images            = $data['product_images']    ?? null;
        $this->tags                      = $data['tags']              ?? null;
        $this->categories                = $data['categories']        ?? null;
        $this->variant_groups            = array_map(fn($group) => new VariantGroupDTO($group), $data['variant_groups'] ?? []);
        $this->skus                      = array_map(fn($sku) => new SkuDTO($sku), $data['skus'] ?? []);
        $this->deleted_file_ids          = $data['deleted_file_ids']          ?? null;
        $this->deleted_variant_group_ids = $data['deleted_variant_group_ids'] ?? null;
        $this->deleted_sku_ids           = $data['deleted_sku_ids']           ?? null;
        $this->deleted_variant_ids       = $data['deleted_variant_ids']       ?? null;
    }
}