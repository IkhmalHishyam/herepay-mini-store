<?php

namespace App\Services\FileManager\Tools\CWebP\DTO;

use App\Services\FileManager\Tools\CWebP\Traits\Helper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ConvertImageToWebPDTO
{
    use Helper;

    public UploadedFile|string $file;

    public int $quality;

    public string $file_name;

    public string $extension = 'webp';

    public function __construct(
        UploadedFile|string $file,
        ?int $quality = null,
        ?string $file_name = null,
    ) {
        $this->file      = $file;
        $this->quality   = self::checkQuality($quality ?? 80);
        $this->file_name = $file_name ?? Str::ulid().'_webp_output';
    }
}
