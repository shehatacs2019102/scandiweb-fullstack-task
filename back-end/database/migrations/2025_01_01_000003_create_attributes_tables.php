<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('type');
        });

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('display_value')->nullable();
            $table->string('value')->nullable();

            $table->unique(['attribute_id', 'value']);
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->string('product_id');
            $table->foreignId('attribute_value_id')->constrained()->cascadeOnDelete();

            $table->primary(['product_id', 'attribute_value_id']);
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
    }
};
