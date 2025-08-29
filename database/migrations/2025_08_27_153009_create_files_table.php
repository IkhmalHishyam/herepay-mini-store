<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try
        {
            Schema::create('files', function (Blueprint $table)
            {
                $table->ulid('id')->primary();
                $table->string('fileable')->index();
                $table->string('file_name');
                $table->string('file_path');
                $table->float('file_size')->default(0);
                $table->string('file_extension')->nullable();
                $table->string('file_mime')->nullable();
                $table->string('access_level')->index();
                $table->string('driver');

                // Polymorphic
                $table->ulidMorphs('fileable');

                // Tracks
                $table->softDeletes();
                $table->timestamps();
            });
        } catch (\Exception $e)
        {
            self::down();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
