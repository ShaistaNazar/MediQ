<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_providers', function (Blueprint $table) {
            $table->increments('id');
            $table->UnsignedInteger('service_provider_id');
            $table->UnsignedInteger('city_id');
            $table->integer('price_per_hour');
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_to')->nullable();
            $table->integer('status');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('service_providers', function($table) {
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('service_provider_id')->references('id')->on('service_provider_info')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_providers');
    }
}
