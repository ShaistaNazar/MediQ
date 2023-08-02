<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->increments('id');
            $table->string('full_name',100);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->char('player_id',255)->nullable();
            $table->char('session_id',255)->nullable();
            $table->char('social_access_token',255)->nullable();
            $table->string('social_id',255)->nullable();
            $table->char('login_type',50)->default('regular');
            $table->char('phone',12);
            $table->string('password')->nullable();
            $table->string('gender');
            $table->string('dob');
            $table->integer('is_active')->default(0);
            $table->integer('activation_code')->nullable();
            $table->UnsignedInteger('role_id')->default(3);
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->UnsignedInteger('shipping_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('users', function($table) {
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('shipping_id')->references('id')->on('shipping_details')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
