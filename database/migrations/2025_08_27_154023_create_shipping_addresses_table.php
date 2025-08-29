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
            Schema::create('shipping_addresses', function (Blueprint $table) 
            {
                $table->ulid('id')->primary();
                $table->string('name');
                $table->string('email');
                $table->string('phone');
                $table->string('address_1');
                $table->string('address_2')->nullable();
                $table->string('city');
                $table->string('state');
                $table->string('postcode');
                $table->string('country')->nullable();
                $table->boolean('is_default')->default(false);

                // Foreign Keys
                $table->foreignUlid('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

                // Tracks
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
        Schema::dropIfExists('shipping_addresses');
    }
};
