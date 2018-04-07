<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAdministratorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('administrator', function (Blueprint $table) {
            $table->integer('status')->default(1)->comment('1有效 0无效')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('administrator', function (Blueprint $table) {
            $table->integer('status')->comment('1有效 0无效')->change();
        });
    }
}
