<?php

namespace App\Services\FileManager\Tools\CWebP\Contracts;

use App\Services\FileManager\Tools\CWebP\CWebPFoundation;
use App\Services\FileManager\Tools\CWebP\DTO\ConvertImageToWebPDTO;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ConvertImageToWebP extends CWebPFoundation
{
    public function handle(ConvertImageToWebPDTO $serviceDTO): static
    {
        $this->setQuality($serviceDTO->quality);
        $this->setInput($serviceDTO->file);
        $this->setOutput($serviceDTO->file_name, $serviceDTO->extension);

        Log::info("Command : $this->command");

        return $this;
    }

    // *****************************************************************************************************************
    // GETTERS
    // *****************************************************************************************************************

    public function getOutputFilePath(): string
    {
        return $this->output_file_path;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    protected function setQuality(int $quality): void
    {
        $this->command .= " -q $quality";
    }

    protected function setInput(UploadedFile|string $file): void
    {
        $file instanceof UploadedFile
            ? $this->command .= " {$file->getRealPath()}"
            : $this->command .= " $file";
    }

    protected function setOutput(string $file_name, string $extension): void
    {
        $this->output_file_path = "{$this->output_path}/{$file_name}.{$extension}";

        $this->command .= " -o $this->output_file_path";
    }
}
