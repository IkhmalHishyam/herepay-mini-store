<?php

namespace App\Services\FileManager\Tools\CWebP;

class CWebPFoundation
{
    protected string $command;

    protected string $output_path;

    protected string $output_file_path;

    public function __construct()
    {
        $this->command     = config('file_manager.third-parties.cwebp.executable_path').'/cwebp';
        $this->output_path = storage_path(config('file_manager.third-parties.cwebp.output_path'));

        if (! is_dir($this->output_path))
        {
            mkdir($this->output_path, 0777, true);
        }
    }

    // *****************************************************************************************************************
    // SETTERS
    // *****************************************************************************************************************

    public function setOutputPath(string $output_path): static
    {
        $this->output_path = $output_path;

        return $this;
    }

    // *****************************************************************************************************************
    // HELPERS
    // *****************************************************************************************************************

    public function executeCommand(): void
    {
        $output     = [];
        $return_var = 0;

        exec($this->command.' 2>&1', $output, $return_var);

        if ($return_var !== 0)
        {
            throw new \Exception('Error while executing command: '.implode("\n", $output));
        }
    }
}
