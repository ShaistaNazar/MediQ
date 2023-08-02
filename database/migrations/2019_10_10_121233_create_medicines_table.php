<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('medicine_name',100);
            $table->string('primary_use', 255);
            $table->string('brand_logo', 191);
            $table->UnsignedInteger('category_id');
            $table->integer('available_quantity');
            $table->integer('price');
            $table->timestamp('expairy_date');
            $table->char('medicine_image')->nullable();
            $table->char('brand')->nullable();
            $table->integer('is_prescription_req');
            $table->integer('status');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('medicines', function($table) {
            $table->foreign('category_id')->references('id')->on('medicines_categories')->onDelete('cascade');
            // $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade');
            // $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
        });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicines');
    }
}
