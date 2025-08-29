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
            Schema::create('invoices', function (Blueprint $table)
            {
                $table->ulid('id')->primary();
                $table->string('invoice_number')->unique();
                $table->float('amount');
                $table->string('status');
                $table->dateTime('paid_at')->nullable();

                // Foreign Keys
                $table->foreignUlid('order_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('invoices');
    }
};
