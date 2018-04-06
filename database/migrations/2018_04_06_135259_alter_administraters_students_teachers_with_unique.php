<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAdministratersStudentsTeachersWithUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('administrators', function (Blueprint $table) {
            $table->unique('login_name');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->unique('stuNo');
        });
        Schema::table('teachers', function (Blueprint $table) {
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('administrators', function (Blueprint $table) {
            $table->dropUnique('login_name');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('stuNo');
        });
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropUnique('name');
        });
    }
}
