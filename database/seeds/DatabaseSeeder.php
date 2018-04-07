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
        \Illuminate\Support\Facades\DB::table('administrator')->insert([
            'nickname' => '张三',
            'mobile' => '13211111111',
            'login_name' => 'admin',
            'password' => '9e220fd633e62b119615e0bdcaf01d8b',
            'salt' => 'oZbzvJ+chEdaU(SE'
        ]);
        \Illuminate\Support\Facades\DB::table('teachers')->insert([
            'teacherNo' => 't01',
            'name' => '李四',
            'sex' => 1,
            'password' => 'e159c0d677a749cd3ed2e8fdf0425878',
            'salt' => 'iU&WEa+lVlQg@*zV',
        ]);
        \Illuminate\Support\Facades\DB::table('students')->insert([
            'stuNo' => 's01',
            'name' => '王五',
            'sex' => 1,
            'password' => '6feb295428d1f31c04b9dda8761d9efe',
            'salt' => 'rZU%&AJ(@n$+u*mx',
        ]);
    }
}
