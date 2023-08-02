<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->UnsignedInteger('user_id');
            $table->string('order_number')->unique();
            $table->UnsignedInteger('delivery_type_id');
            $table->UnsignedInteger('shipping_id');
            $table->UnsignedInteger('billing_id');
            $table->UnsignedInteger('payment_method_id');
            $table->boolean('status');
            $table->bigInteger('total_amount');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('orders', function($table) {

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('delivery_type_id')->references('id')->on('delivery_types')->onDelete('cascade');
            $table->foreign('shipping_id')->references('id')->on('shipping_details')->onDelete('cascade');
            $table->foreign('billing_id')->references('id')->on('billing_details')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
