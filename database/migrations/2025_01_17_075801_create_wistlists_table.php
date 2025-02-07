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
			$table->integer('order_id')->nullable();
			$table->string('email_address')->nullable();
			$table->string('relationship')->nullable();
			$table->date('birthdate')->nullable();
			$table->date('aniversary')->nullable();
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
