<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceProviderInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_provider_info', function (Blueprint $table) {
            $table->increments('id');
            $table->UnsignedInteger('services_id');
            $table->string('full_name',100);
            $table->string('email')->unique();
            $table->char('phone',12);
            $table->string('gender');
            $table->string('dob');
            $table->integer('status');
            $table->string('avatar')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('service_provider_info', function($table) {
            $table->foreign('services_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_provider_info');
    }
}
