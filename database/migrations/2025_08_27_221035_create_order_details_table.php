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
            Schema::create('order_details', function (Blueprint $table)
            {
                $table->ulid('id')->primary();
                $table->string('customer_name');
                $table->string('customer_email');
                $table->string('customer_phone');
                $table->string('shipping_address_1');
                $table->string('shipping_address_2')->nullable();
                $table->string('shipping_city');
                $table->string('shipping_state');
                $table->string('shipping_postcode');
                $table->string('shipping_country')->nullable();
                $table->longText('order_notes')->nullable();

                // Foreign Key
                $table->foreignUlid('order_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

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
        Schema::dropIfExists('order_details');
    }
};
