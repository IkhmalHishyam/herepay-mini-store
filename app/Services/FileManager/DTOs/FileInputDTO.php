<?php

namespace App\Services\FileManager\DTOs;

use App\Models\User;
use App\Services\FileManager\Enums\FileablePrefixes;
use App\Services\FileManager\Enums\FileAccessLevelEnum;
use Illuminate\Http\UploadedFile;

class FileInputDTO
{
    public ?User $user;

    public object $model;

    public UploadedFile|array|null $files;

    public FileablePrefixes $fileable;

    public ?string $file_name;

    public FileAccessLevelEnum $access_level;

    public string $acl;

    public ?array $custom_blueprint = null;

    public bool $is_singular;

    public FileFilterOptionDTO $filterOptionDTO;

    public function __construct(
        ?User $user,
        object $model,
        UploadedFile|array|null $files,
        FileablePrefixes $fileable,
        ?string $file_name = null,
        FileAccessLevelEnum $access_level = FileAccessLevelEnum::PUBLIC,
        string $acl = 'public-read',
        ?array $custom_blueprint = null,
        bool $is_singular = false,
        ?FileFilterOptionDTO $filterOptionDTO = null,
    ) {
        $this->user             = $user;
        $this->model            = $model;
        $this->files            = $files;
        $this->fileable         = $fileable;
        $this->file_name        = $file_name;
        $this->access_level     = $access_level;
        $this->acl              = $acl;
        $this->custom_blueprint = $custom_blueprint;
        $this->is_singular      = $is_singular;
        $this->filterOptionDTO  = $filterOptionDTO ?? new FileFilterOptionDTO;
    }
}
