<?php

namespace App\Services\FileManager\Contracts;

use App\Services\FileManager\Exceptions\FMMissingDirectoryOriginException;
use App\Services\FileManager\FileManagerFoundation;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FMDeleteDirectory extends FileManagerFoundation
{
    protected string $log_tag = '[FMDeleteDirectory]';

    public function handle(string $directory_path): static
    {
        $this->deleteDirectory($directory_path);

        return $this;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function deleteDirectory(string $directory_path): void
    {
        if (Storage::disk(config('filesystems.default'))->exists($directory_path))
        {
            Storage::disk(config('filesystems.default'))->deleteDirectory($directory_path);
        } else
        {
            throw new FMMissingDirectoryOriginException(
                message : 'File not found in storage',
                code    : Response::HTTP_NOT_FOUND
            );
        }
    }
}
