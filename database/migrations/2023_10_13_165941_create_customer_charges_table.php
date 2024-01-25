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
        Schema::create('customer_charges', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id');
            $table->string('reference');
            $table->string('amount');
            $table->string('delivery_fee');
            $table->string('service_charge')->nullable();
            $table->string('discount')->default(0);
            $table->string('payment_method')->nullable();
            $table->string('location');
            $table->string('payingfor');
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
        Schema::dropIfExists('customer_charges');
    }
};
