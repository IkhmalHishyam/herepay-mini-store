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
            Schema::create('transactions', function (Blueprint $table)
            {
                $table->ulid('id')->primary();
                $table->string('transaction_no')->unique();
                $table->float('amount');
                $table->string('currency');
                $table->string('payment_method');
                $table->string('status');
                $table->dateTime('paid_at')->nullable();

                // Foreign Keys
                $table->foreignUlid('invoice_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

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
        Schema::dropIfExists('transactions');
    }
};
