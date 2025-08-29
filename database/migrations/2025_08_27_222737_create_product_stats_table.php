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
            Schema::create('product_stats', function (Blueprint $table)
            {
                $table->ulid('id')->primary();
                $table->float('total_revenue')->default(0);
                $table->integer('total_sold_in_units')->default(0);
                $table->dateTime('last_sold_at')->nullable();

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
        Schema::dropIfExists('product_stats');
    }
};
