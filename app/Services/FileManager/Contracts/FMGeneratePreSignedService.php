<?php

namespace App\Services\FileManager\Contracts;

use App\Services\FileManager\FileManagerFoundation;
use Illuminate\Support\Facades\Storage;

class FMGeneratePreSignedService extends FileManagerFoundation
{
    protected string $log_tag = '[FMGeneratePreSignedService]';

    protected ?string $temporary_url;

    public function handle(string $file_path, ?\DateTimeInterface $expired_at = null, bool $strict): static
    {
        $this->generatePreSignedUrl($file_path, $expired_at, $strict);

        return $this;
    }

    // *****************************************************************************************************************
    // ACCESSORS
    // *****************************************************************************************************************

    public function getTemporaryUrl(): string
    {
        return $this->temporary_url;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function generatePreSignedUrl(string $file_path, ?\DateTimeInterface $expired_at = null, bool $strict): void
    {
        $this->temporary_url = Storage::disk(config('filesystems.default'))->temporaryUrl($file_path, $expired_at);
    }
}
