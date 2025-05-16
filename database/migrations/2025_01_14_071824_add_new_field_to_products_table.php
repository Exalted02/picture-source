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
        Schema::table('products', function (Blueprint $table) {
            $table->integer('size')->nullable()->after('subcategory');
			$table->integer('color')->nullable()->after('size');
			$table->integer('orientation')->nullable()->after('color')->comment('1=Portrait, 2=Landscape, 3=Square');
			$table->integer('length')->nullable()->after('orientation');
			$table->integer('width')->nullable()->after('length');
			$table->integer('depth')->nullable()->after('width');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
