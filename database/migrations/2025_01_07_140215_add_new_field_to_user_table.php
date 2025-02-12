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
          $table->string('status')->after('otp')->nullable();
          $table->string('auth_provider')->after('status')->nullable();
          $table->string('auth_provider_id')->after('auth_provider')->nullable();
          $table->tinyInteger('profile_verified')->after('auth_provider_id')->default(0)->comment('0 = Not verified, 1 = Verified');
          $table->string('stripe_paymethod_id')->after('profile_verified')->nullable();
		  $table->string('latitude')->after('stripe_paymethod_id')->nullable();
		  $table->string('longitude')->after('latitude')->nullable();
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
