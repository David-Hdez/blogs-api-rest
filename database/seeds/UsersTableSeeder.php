<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
            'name' => 'VEMBIT',
            'email' => 'david.alfredo.hdez@outlook.com',
            'password' => bcrypt('secret'),
        ]);

        DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => 'davidalfredo.hdez@gmail.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
