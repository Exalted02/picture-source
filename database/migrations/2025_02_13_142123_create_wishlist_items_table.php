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
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->id();
			$table->integer('wishlist_id')->nullable();
			$table->integer('product_id')->nullable();
			$table->string('product_name')->nullable();
			$table->string('product_code')->nullable();
			$table->integer('quantity')->nullable();
			$table->double('price', 10, 2)->nullable();
			$table->integer('color_id')->nullable();
			$table->integer('size_id')->nullable();
			$table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};
