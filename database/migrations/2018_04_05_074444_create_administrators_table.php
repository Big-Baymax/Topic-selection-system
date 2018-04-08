<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdministratorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrators', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30)->comment('用户名');
            $table->string('mobile', 11)->comment('手机号');
            $table->string('login_name', 20)->comment('登录名');
            $table->string('password', 32)->comment('登录密码');
            $table->string('salt', 16)->comment('加密盐');
            $table->tinyInteger('status')->default(1)->comment('1 有效 0无效');
            $table->timestamps();
            $table->unique('login_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('administrator');
    }
}
