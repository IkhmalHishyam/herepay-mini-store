<?php

namespace App\Services\FileManager\Contracts;

use App\Models\Supports\File;
use App\Modules\ModelAccess\FileData;
use App\Services\FileManager\DTOs\FileFilterOptionDTO;
use App\Services\FileManager\DTOs\FileInputDTO;
use App\Services\FileManager\DTOs\FilePropertyDTO;
use App\Services\FileManager\FileManagerFoundation;
use App\Services\FileManager\Helpers\ImageConvertor;
use App\Services\FileManager\Helpers\ImageResizer;
use App\Services\FileManager\Jobs\FileUploadRollbackJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FMStoreService extends FileManagerFoundation
{
    protected string $log_tag = '[FMStoreService]';

    protected bool $single_mode;

    protected UploadedFile $file;

    protected FilePropertyDTO $filePropertyDTO;

    protected array $uploaded_files = [];

    protected File $fileModel;

    public function handle(FileInputDTO $fileInputDTO): static
    {
        if (! $fileInputDTO->files)
        {
            return $this;
        }

        $this->deleteExistingFiles($fileInputDTO);
        $this->checkStoringMode($fileInputDTO);

        if ($this->single_mode)
        {
            Log::info("$this->log_tag Started - Single Mode Upload");

            $this->processFilters($fileInputDTO->files, $fileInputDTO->filterOptionDTO);
            $this->extractFileProperty($this->file, $fileInputDTO);

            $this->fileModel = $this->filePropertyDTO->model->files()->create([
                'fileable'       => $this->filePropertyDTO->fileable,
                'file_name'      => $this->filePropertyDTO->file_name,
                'file_path'      => $this->filePropertyDTO->file_path,
                'file_size'      => $this->filePropertyDTO->file_size,
                'file_extension' => $this->filePropertyDTO->file_extension,
                'file_mime'      => $this->filePropertyDTO->file_type,
                'access_level'   => $this->filePropertyDTO->access_level,
                'driver'         => $this->filePropertyDTO->mode,
            ]);

            $this->putFileToStorage();

            Log::info("$this->log_tag Finished - Single Mode Upload");
        } else
        {
            Log::info("$this->log_tag Started - Multiple Mode Upload");

            foreach ($fileInputDTO->files as $file)
            {
                $this->processFilters($file, $fileInputDTO->filterOptionDTO);
                $this->extractFileProperty($this->file, $fileInputDTO);

                $this->fileModel = $this->filePropertyDTO->model->files()->create([
                    'fileable'       => $this->filePropertyDTO->fileable,
                    'file_name'      => $this->filePropertyDTO->file_name,
                    'file_path'      => $this->filePropertyDTO->file_path,
                    'file_size'      => $this->filePropertyDTO->file_size,
                    'file_extension' => $this->filePropertyDTO->file_extension,
                    'file_mime'      => $this->filePropertyDTO->file_type,
                    'access_level'   => $this->filePropertyDTO->access_level,
                    'driver'         => $this->filePropertyDTO->mode,
                ]);

                $this->putFileToStorage();
            }

            Log::info("$this->log_tag Finished - Multiple Mode Upload");
        }

        ! empty($this->uploaded_files) ? FileUploadRollbackJob::dispatch($this->uploaded_files)->afterResponse() : null;

        return $this;
    }

    // *****************************************************************************************************************
    // ACCESSORS
    // *****************************************************************************************************************

    public function getUploadedFiles(): array
    {
        return $this->uploaded_files;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function checkStoringMode(FileInputDTO $fileInputDTO): void
    {
        $this->single_mode = ! is_array($fileInputDTO->files);
    }

    protected function deleteExistingFiles(FileInputDTO $fileInputDTO): void
    {
        if ($fileInputDTO->is_singular && $fileInputDTO->model->files->isNotEmpty())
        {
            Log::info("$this->log_tag Started - Delete Existing Files");

            foreach ($fileInputDTO->model->files->where('fileable', $fileInputDTO->fileable->value) as $file)
            {
                Log::info("$this->log_tag Deleting existing file: $file->id [{$file->fileable->value}]");

                $fileManagerDeleteService = new FMDeleteService;
                $fileManagerDeleteService->handle($file);
            }

            Log::info("$this->log_tag Finished - Delete Existing Files");
        }
    }

    protected function processFilters(UploadedFile $file, FileFilterOptionDTO $filterOptionDTO): void
    {
        $this->file = $file;

        // if ($filterOptionDTO->convert_to_webp)
        // {
        //     $imageConvertor = new ImageConvertor;
        //     $imageConvertor->handle($file);

        //     $this->file = $imageConvertor->getNewFile();
        // }

        // if ($filterOptionDTO->resize)
        // {
        //     $imageResizer = new ImageResizer;
        //     $imageResizer->handle($file);

        //     $this->file = $imageResizer->getNewFile();
        // }
    }

    protected function extractFileProperty(UploadedFile $file, FileInputDTO $fileInputDTO): void
    {
        $this->filePropertyDTO = new FilePropertyDTO($file, $fileInputDTO);
    }

    protected function putFileToStorage(): void
    {
        $this->uploaded_files[] = [
            'file_id'   => $this->fileModel->id,
            'file_path' => Storage::disk(config('filesystems.default'))->putFileAs(
                path : $this->filePropertyDTO->directory_path,
                file : $this->file,
                name : $this->filePropertyDTO->file_name
            ),
        ];
    }
}
