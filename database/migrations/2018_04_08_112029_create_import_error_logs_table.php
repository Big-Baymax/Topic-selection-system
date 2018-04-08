<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImportErrorLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_error_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table', 20)->comment('表名');
            $table->string('stuNo', 100)->nullable()->comment('学号');
            $table->string('teacherNo', 100)->nullable()->comment('教师工号');
            $table->string('name', 100)->nullable()->comment('姓名');
            $table->string('department', 100)->nullable()->comment('系别');
            $table->string('sex', 100)->nullable()->comment('性别');
            $table->integer('list')->default(1)->comment('识别组批量插入操作');
            $table->string('reason', 30)->comment('原因');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_error_logs');
    }
}
