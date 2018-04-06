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
        Schema::table('administrators', function (Blueprint $table) {
            $table->renameColumn('login_salt', 'salt');
            $table->renameColumn('login_pwd', 'password');
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
            $table->renameColumn('salt', 'login_salt');
            $table->renameColumn('password', 'login_pwd');
        });
    }
}
