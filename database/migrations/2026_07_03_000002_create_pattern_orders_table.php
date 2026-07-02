<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pattern_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreignId('pattern_product_id')->constrained('pattern_products');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('amount_in_paise');
            $table->string('status')->default('pending');
            $table->string('payment_provider')->nullable();
            $table->string('provider_payment_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'pattern_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pattern_orders');
    }
};
