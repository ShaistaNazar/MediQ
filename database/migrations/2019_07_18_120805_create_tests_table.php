<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->increments('id');
            $table->char('test_name',50);
            $table->UnsignedInteger('lab_id');
            $table->UnsignedInteger('testcategory_id');
            $table->integer('price');
            $table->string('description');
            $table->string('logo');
            $table->integer('is_prescription_req');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('tests', function($table) {
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
            $table->foreign('testcategory_id')->references('id')->on('test_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tests');
    }
}
