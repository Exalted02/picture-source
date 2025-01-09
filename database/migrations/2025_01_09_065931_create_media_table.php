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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
			$table->integer('media_source_id')->nullable();
			$table->tinyInteger('media_type')->comment('1=category,2=subcategory,3=products');
			$table->string('image')->nullable();
			$table->tinyInteger('status')->comment('0=inactive,1=active,2=delete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
