<?php

namespace App\Services\FileManager;

use App\Services\FileManager\Contracts\FMDeleteDirectory;
use App\Services\FileManager\Contracts\FMDeleteService;
use App\Services\FileManager\Contracts\FMGenerateDirectoryService;
use App\Services\FileManager\Contracts\FMGeneratePreSignedService;
use App\Services\FileManager\Contracts\FMGeneratePublicURL;
use App\Services\FileManager\Contracts\FMMoveFile;
use App\Services\FileManager\Contracts\FMStoreService;
use App\Services\FileManager\DTOs\FileInputDTO;
use Illuminate\Support\Facades\Log;

class FileManager extends FileManagerFoundation
{
    protected string $log_tag = '[FileManager]';

    public function store(FileInputDTO $fileInputDTO): array
    {
        Log::info("$this->log_tag[store] Started");

        $service = new FMStoreService;
        $service->handle($fileInputDTO);

        Log::info("$this->log_tag[store] Finished");

        return $service->getUploadedFiles();
    }

    public function delete(string $file_id): static
    {
        Log::info("$this->log_tag[delete] Started");

        $service = new FMDeleteService;
        $service->handle($file_id);

        Log::info("$this->log_tag[delete] Finished");

        return $this;
    }

    public function bulkDelete(?array $file_ids): static
    {
        Log::info("$this->log_tag[bulkDelete] Started");

        if (is_null($file_ids))
        {
            Log::info("$this->log_tag[bulkDelete] Finished - No file to delete");

            return $this;
        }

        foreach ($file_ids as $file_id)
        {
            $service = new FMDeleteService;
            $service->handle($file_id);
        }

        Log::info("$this->log_tag[bulkDelete] Finished");

        return $this;
    }

    public function generatePublicURL(string $file_path, bool $strict = true): ?string
    {
        $service = new FMGeneratePublicURL;
        $service->handle($file_path, $strict);

        return $service->getPublicURL();
    }

    public function generatePreSignedURL(string $file_path, ?\DateTimeInterface $expired_at = null, bool $strict = true): ?string
    {
        $service = new FMGeneratePreSignedService;
        $service->handle($file_path, $expired_at, $strict);

        return $service->getTemporaryUrl();
    }

    public function moveFile(string $old_file_path, string $new_file_path): static
    {
        Log::info("$this->log_tag[moveFile] Started");

        $service = new FMMoveFile;
        $service->handle($old_file_path, $new_file_path);

        Log::info("$this->log_tag[moveFile] Finished");

        return $this;
    }

    public function createDirectory(string $directory_path): static
    {
        Log::info("$this->log_tag[createDirectory] Started");

        $service = new FMGenerateDirectoryService;
        $service->handle($directory_path);

        Log::info("$this->log_tag[createDirectory] Finished");

        return $this;
    }

    public function deleteDirectory(string $directory_path): static
    {
        Log::info("$this->log_tag[deleteDirectory] Started");

        $service = new FMDeleteDirectory;
        $service->handle($directory_path);

        Log::info("$this->log_tag[deleteDirectory] Finished");

        return $this;
    }
}
