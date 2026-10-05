<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('in_stock');
            $table->json('gallery')->nullable();
            $table->foreignId('category_id')->constrained();
            $table->string('brand')->nullable();
        });

        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->decimal('amount', 10, 2);
            $table->string('currency_label', 10)->nullable();
            $table->string('currency_symbol', 5)->nullable();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prices');
        Schema::dropIfExists('products');
    }
};
