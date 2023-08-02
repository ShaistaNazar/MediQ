<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_equipments', function (Blueprint $table) {
            $table->increments('id');
            $table->UnsignedInteger('equipment_id');
            $table->UnsignedInteger('city_id');
            $table->timestamps();
        });
        Schema::table('city_equipments', function($table) {

            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('equipment_id')->references('id')->on('equipments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_equipments');
    }
}
