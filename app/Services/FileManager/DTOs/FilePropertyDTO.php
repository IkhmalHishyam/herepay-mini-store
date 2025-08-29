<?php

namespace App\Services\FileManager\DTOs;

use App\Services\FileManager\Enums\FileAccessLevelEnum;
use App\Services\FileManager\Traits\FileBlueprints;
use App\Services\FileManager\Traits\FileHelpers;
use Illuminate\Http\UploadedFile;

class FilePropertyDTO
{
    use FileBlueprints, FileHelpers;

    public string $base_model;

    public object $model;

    public UploadedFile $file;

    public object $fileable;

    public string $file_name;

    public string $directory_path;

    public string $file_path;

    public int $file_size;

    public string $file_type;

    public string $file_extension;

    public string $driver;

    public FileAccessLevelEnum $access_level;

    public string $acl;

    public string $mode;

    public function __construct(UploadedFile $file, FileInputDTO $fileInputDTO)
    {
        $model            = $fileInputDTO->model;
        $base_model       = class_basename($model);
        $fileable         = $fileInputDTO->fileable;
        $file_extension   = $file->getClientOriginalExtension();
        $file_name        = $fileInputDTO->file_name ? $fileInputDTO->file_name.".$file_extension" : self::getGenericFileName($file);
        $file_size        = self::geSizeByKB($file);
        $file_type        = $file->getMimeType();
        $driver           = config('filesystems.default');
        $access_level     = $fileInputDTO->access_level;
        $acl              = $fileInputDTO->acl;
        $custom_blueprint = $fileInputDTO->custom_blueprint;
        $directory_path   = $custom_blueprint ? self::getCustomBlueprint($custom_blueprint) : self::getPredefinedBlueprint(
            user         : $fileInputDTO->user,
            access_level : $access_level,
            base_model   : $base_model,
            model_id     : $model->id,
            fileable     : $fileable->value
        );
        $file_path = $directory_path.$file_name;
        $mode      = config('filesystems.default');

        $this->base_model     = $base_model;
        $this->model          = $model;
        $this->file           = $file;
        $this->fileable       = $fileable;
        $this->file_name      = $file_name;
        $this->directory_path = $directory_path;
        $this->file_path      = $file_path;
        $this->file_size      = $file_size;
        $this->file_type      = $file_type;
        $this->file_extension = $file_extension;
        $this->driver         = $driver;
        $this->access_level   = $access_level;
        $this->acl            = $acl;
        $this->mode           = $mode;
    }
}
