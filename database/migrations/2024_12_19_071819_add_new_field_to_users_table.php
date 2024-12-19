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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('name')->nullable();
			$table->string('last_name')->after('first_name')->nullable();
			$table->string('company_name')->after('password')->nullable();
			$table->string('address')->after('company_name')->nullable();
			$table->string('city')->after('address')->nullable();
			$table->string('state')->after('city')->nullable();
			$table->string('zipcode')->after('state')->nullable();
			$table->string('phone_number')->after('zipcode')->nullable();
			$table->string('upload_tax_lisence')->after('phone_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
