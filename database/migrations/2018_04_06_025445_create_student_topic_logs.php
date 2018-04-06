<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTopicLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_topic_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->comment('学生ID');
            $table->integer('topic_id')->comment('选题ID');
            $table->timestamp('create_at')->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_topic_logs');
    }
}
