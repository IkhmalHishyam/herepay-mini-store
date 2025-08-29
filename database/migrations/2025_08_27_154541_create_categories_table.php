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
            Schema::create('categories', function (Blueprint $table) 
            {
                $table->ulid('id')->primary();
                $table->string('name');

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
        Schema::dropIfExists('categories');
    }
};
