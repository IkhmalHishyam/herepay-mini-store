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
            Schema::create('product_tags', function (Blueprint $table)
            {
                // Foreign Keys
                $table->foreignUlid('product_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignUlid('tag_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            });
        }
        catch (\Exception $e)
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
        Schema::dropIfExists('product_tags');
    }
};
