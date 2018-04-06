<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreteTopicCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->comment('分类名称');
            $table->tinyInteger('weight')->comment('权重');
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
        Schema::dropIfExists('topic_categories');
    }
}
