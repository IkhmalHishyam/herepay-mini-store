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
            Schema::create('customer_stats', function (Blueprint $table)
            {
                $table->ulid('id')->primary();
                $table->integer('total_orders')->default(0);
                $table->float('total_spent')->default(0);
                $table->float('average_order_value')->default(0);
                $table->dateTime('last_order_date')->nullable();

                // Foreign Keys
                $table->foreignUlid('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

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
        Schema::dropIfExists('customer_stats');
    }
};
