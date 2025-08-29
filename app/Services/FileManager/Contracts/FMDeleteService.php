<?php

namespace App\Services\FileManager\Contracts;

use App\Models\Supports\File;
use App\Services\FileManager\Exceptions\FMMissingFileOriginException;
use App\Services\FileManager\FileManagerFoundation;
use App\Services\FileManager\Jobs\FileDeletionJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FMDeleteService extends FileManagerFoundation
{
    protected string $log_tag = '[FMDeleteService]';

    public function handle(File|string $file, bool $strict = false): static
    {
        if ($file instanceof File)
        {
            Log::info("$this->log_tag Started - Permanent Deletion");

            $file->forceDelete();

            $this->deleteFromStorage($file->file_path, $strict);

            Log::info("$this->log_tag Finished - Permanent Deletion");
        } else
        {
            Log::info("$this->log_tag Started - Soft Deletion");

            $file = File::findOrFail($file);
            $file->forceDelete();

            FileDeletionJob::dispatch($file)->afterResponse();

            Log::info("$this->log_tag Finished - Soft Deletion");
        }

        return $this;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function deleteFromStorage(string $file_path, bool $strict): void
    {
        if (Storage::disk(config('filesystems.default'))->exists($file_path))
        {
            Storage::disk(config('filesystems.default'))->delete($file_path);
        } else
        {
            if ($strict)
            {
                throw new FMMissingFileOriginException(
                    message : 'File not found in storage',
                    code    : Response::HTTP_NO_CONTENT
                );
            }
        }
    }
}
