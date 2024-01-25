<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('address');
            $table->string('phone_number');
            $table->string('refferal_id');
            $table->string('profile_picture')->nullable();
            $table->integer('point')->default(0);
            $table->integer('wallet')->default(0);
            $table->timestamp('login_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('verification_token');
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
