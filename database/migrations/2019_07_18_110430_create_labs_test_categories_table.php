<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabsTestCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labs_test_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->UnsignedInteger('lab_id');
            $table->UnsignedInteger('testcategory_id');
            $table->timestamps();
        });
        Schema::table('labs_test_categories', function($table) {
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
        Schema::dropIfExists('labs_test_categories');
    }
}
