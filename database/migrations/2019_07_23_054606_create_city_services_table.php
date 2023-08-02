<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_services', function (Blueprint $table) {

            $table->increments('id');
            $table->UnsignedInteger('service_id');
            $table->UnsignedInteger('city_id');
            $table->timestamps();
        });

        Schema::table('city_services', function($table) {

            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services_types')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_services');
    }
}
