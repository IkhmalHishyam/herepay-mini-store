<?php

namespace App\Services\FileManager\Contracts;

use App\Services\FileManager\Exceptions\FMMissingFileOriginException;
use App\Services\FileManager\FileManagerFoundation;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FMMoveFile extends FileManagerFoundation
{
    protected string $log_tag = '[FMMoveFile]';

    public function handle(string $old_file_path, string $new_file_path): static
    {
        $this->moveFile($old_file_path, $new_file_path);

        return $this;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function moveFile(string $old_file_path, string $new_file_path): void
    {
        if (Storage::disk(config('filesystems.default'))->exists($old_file_path))
        {
            Storage::disk(config('filesystems.default'))->move($old_file_path, $new_file_path);
        } else
        {
            throw new FMMissingFileOriginException(
                message : 'File not found in storage',
                code    : Response::HTTP_NOT_FOUND
            );
        }
    }
}
