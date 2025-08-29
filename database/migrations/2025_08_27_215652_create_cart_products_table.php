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
            Schema::create('cart_products', function (Blueprint $table)
            {
                $table->string('name');
                $table->string('sku')->nullable();
                $table->float('price_per_unit')->default(0);
                $table->integer('quantity')->default(1);
                $table->float('total_price')->default(0);

                // Foreign Keys
                $table->foreignUlid('product_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
                $table->foreignUlid('cart_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

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
        Schema::dropIfExists('cart_products');
    }
};
