<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTableAndAddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->comment('系名');
            $table->timestamps();
        });
        Schema::table('administrators', function (Blueprint $table) {
            $table->integer('department_id')->after('mobile');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->integer('department_id')->after('name');
        });
        Schema::table('teachers', function (Blueprint $table) {
            $table->integer('department_id')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('departments');
        Schema::table('administrators', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
    }
}
