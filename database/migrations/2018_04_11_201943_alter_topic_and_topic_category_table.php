<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTopicAndTopicCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->integer('department_id')->after('description')->comment('系别ID');
        });
        Schema::table('topic_categories', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('student_topic_logs', function (Blueprint $table) {
            $table->tinyInteger('status')->after('teacher_id')->comment('选题状态');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('topic_categories', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1);
        });
        Schema::table('student_topic_logs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
