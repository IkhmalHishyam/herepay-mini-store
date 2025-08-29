<?php

namespace App\Services\FileManager\Contracts;

use App\Services\FileManager\FileManagerFoundation;
use Illuminate\Support\Facades\Storage;

class FMGeneratePublicURL extends FileManagerFoundation
{
    protected string $log_tag = '[FMGeneratePublicURL]';

    protected ?string $public_url;

    public function handle(string $file_path, bool $strict): static
    {
        $this->generatePublicURL($file_path, $strict);

        return $this;
    }

    // *****************************************************************************************************************
    // ACCESSORS
    // *****************************************************************************************************************

    public function getPublicURL(): ?string
    {
        return $this->public_url;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function generatePublicURL(string $file_path, bool $strict): void
    {
        $this->public_url = Storage::disk(config('filesystems.default'))->url($file_path);
    }
}
