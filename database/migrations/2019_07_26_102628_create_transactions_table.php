<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('merchant_id');
            $table->string('hash_key');
            $table->bigInteger('amount');
            $table->integer('bill_reference');
            $table->string('description');
            $table->string('language');
            $table->string('currency');
            $table->date('expiry_date');
            $table->bigInteger('ref_number');
            $table->integer('txt_number');
            $table->integer('submerchant_id');
            $table->bigInteger('discounted_amount');
            $table->string('discounted_bank');
            $table->date('customer_card_expiry');
            $table->bigInteger('card_cvv');
            $table->string('instrument_type');
            $table->string('pmpf_1');
            $table->string('pmpf_2');
            $table->string('pmpf_3');
            $table->string('response_msg');
            $table->integer('response_code');
            $table->integer('retrival_ref');
            $table->integer('order_id');
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
        Schema::dropIfExists('transactions');
    }
}
