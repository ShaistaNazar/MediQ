<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityLabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_labs', function (Blueprint $table) {
            $table->increments('id');
            $table->UnsignedInteger('lab_id');
            $table->UnsignedInteger('city_id');
            $table->integer('status');
            $table->char('long',50);
            $table->char('lat',50);
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('city_labs', function($table) {
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_labs');
    }
}
