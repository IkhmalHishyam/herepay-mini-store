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
            Schema::create('products', function (Blueprint $table)
            {
                $table->ulid('id')->primary();
                $table->string('name');
                $table->longText('description')->nullable();
                $table->float('price');
                $table->boolean('is_published')->default(false);

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
        Schema::dropIfExists('products');
    }
};
