<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pattern_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('price_in_paise');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pattern_products');
    }
};
