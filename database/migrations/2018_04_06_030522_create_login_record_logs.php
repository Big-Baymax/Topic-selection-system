<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginRecordLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_record_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('target_id')->comment('登录用户id');
            $table->string('target_table')->comment('对应的表');
            $table->string('login_ip', 16)->comment('登录IP');
            $table->timestamp('login_at')->comment('登录时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login_record_logs');
    }
}
