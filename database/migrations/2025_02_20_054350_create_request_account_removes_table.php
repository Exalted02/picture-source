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
        Schema::create('request_account_removes', function (Blueprint $table) {
            $table->id();
			$table->string('email');
			$table->text('region');
			$table->tinyInteger('status')->default(2)->comment('1=approve,2=pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_account_removes');
    }
};
