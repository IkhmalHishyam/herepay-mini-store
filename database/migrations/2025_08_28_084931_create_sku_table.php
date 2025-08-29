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
            Schema::create('sku', function (Blueprint $table)
            {
                $table->ulid('id')->primary();
                $table->string('matrix');
                $table->float('price')->default(0);
                $table->unsignedBigInteger('total_stock')->default(0);

                // Foreign Keys
                $table->foreignUlid('product_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                // Tracks
                $table->softDeletes();
                $table->timestamps();
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
        Schema::dropIfExists('sku');
    }
};
