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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
			$table->integer('user_id')->nullable();
			$table->integer('delivery_address_id')->nullable();
			$table->double('discount_amount', 10, 2)->nullable();
			$table->double('coupon_discount', 10, 2)->nullable();
			$table->double('shipping_charge', 10, 2)->nullable();
			$table->double('order_total', 10, 2)->nullable();
			$table->double('final_amount', 10, 2)->nullable();
			$table->tinyInteger('order_type')->comment('0=normal order,1=wistlist')->nullable();
			$table->tinyInteger('status')->default(1)->comment('1=pending,2=shipped,3=cancel,4=deliver');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
