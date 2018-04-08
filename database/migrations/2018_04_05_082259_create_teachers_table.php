<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('teacherNo', 32)->comment('教师工号');
            $table->string('name', 20)->comment('姓名');
            $table->tinyInteger('sex')->default(0)->comment('性别 1男 2女 0未填写');
            $table->string('password', 32)->comment('密码');
            $table->string('salt', 16)->comment('加密盐');
            $table->tinyInteger('status')->default(1)->comment('状态 1有效 0无效');
            $table->timestamps();
            $table->unique('teacherNo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teachers');
    }
}
