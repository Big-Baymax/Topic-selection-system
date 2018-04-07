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
        Schema::create('administrator', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nickname', 30)->comment('用户名');
            $table->string('mobile', 11)->comment('手机号');
            $table->string('login_name', 20)->comment('登录名');
            $table->string('login_pwd', 32)->comment('登录密码');
            $table->string('login_salt', 16)->comment('加密盐');
            $table->tinyInteger('status')->comment('1 有效 0无效');
            $table->timestamps();
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
