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
        Schema::create('wistlists', function (Blueprint $table) {
            $table->id();
			$table->integer('user_id')->nullable();
			$table->double('discount_amount', 10, 2)->nullable();
			$table->double('coupon_discount', 10, 2)->nullable();
			$table->double('shipping_charge', 10, 2)->nullable();
			$table->double('order_total', 10, 2)->nullable();
			$table->double('final_amount', 10, 2)->nullable();
			$table->tinyInteger('order_type')->comment('0=normal order,1=wistlist')->nullable();
			$table->string('email_address')->nullable();
			$table->string('phone_no')->nullable();
			$table->string('relationship')->nullable();
			$table->date('birthdate')->nullable();
			$table->date('aniversary')->nullable();
			$table->string('facebook_address')->nullable();
			$table->string('instagram_address')->nullable();
			$table->string('tiktok_address')->nullable();
			$table->tinyInteger('status')->default(1)->comment('0=inactive,1=active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wistlists');
    }
};
