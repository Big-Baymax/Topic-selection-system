<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->comment('分类id');
            $table->string('name', 20)->comment('选题名称');
            $table->text('description')->comment('选题描述');
            $table->integer('teacher_id')->comment('所属教师id');
            $table->integer('student_id')->default(0)->comment('所属学生id');
            $table->tinyInteger('status')->comment('状态 1有效 0无效');
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
        Schema::dropIfExists('topics');
    }
}
