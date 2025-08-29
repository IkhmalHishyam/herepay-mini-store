<?php

namespace App\Services\FileManager\Contracts;

use App\Services\FileManager\FileManagerFoundation;
use Illuminate\Support\Facades\Storage;

class FMGenerateDirectoryService extends FileManagerFoundation
{
    protected string $log_tag = '[FMGenerateDirectory]';

    protected string $directory_path;

    public function handle(string $path): static
    {
        $this->generateDirectory($path);

        return $this;
    }

    // *****************************************************************************************************************
    // ACCESSORS
    // *****************************************************************************************************************

    public function getDirectoryPath(): string
    {
        return $this->directory_path;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function generateDirectory(string $path): void
    {
        if (Storage::disk(config('filesystems.default'))->exists($path))
        {
            $this->directory_path = Storage::disk(config('filesystems.default'))->makeDirectory($path);

            return;
        }

        $this->directory_path = $path;
    }
}
