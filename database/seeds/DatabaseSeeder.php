<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        \Illuminate\Support\Facades\DB::table('administrators')->insert([
            'name' => '超级管理员',
            'mobile' => '13328942695',
            'login_name' => 'admin',
            'password' => '9e220fd633e62b119615e0bdcaf01d8b',
            'salt' => 'oZbzvJ+chEdaU(SE'
        ]);
    }
}
