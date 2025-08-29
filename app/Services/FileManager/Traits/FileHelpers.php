<?php

namespace App\Services\FileManager\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait FileHelpers
{
    /**
     * Get Size by KB
     */
    public function geSizeByKB(UploadedFile $file): float
    {
        return $file->getSize() / 1024;
    }

    /**
     * Get File Name
     */
    public function getGenericFileName(UploadedFile $file): string
    {
        return Str::ulid().'.'.$file->getClientOriginalExtension();
    }
}
