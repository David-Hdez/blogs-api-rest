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
            'password' => hash('sha256','secret_47'),
            'role' => 'ROLE_ADMIN',
        ]);

        DB::table('users')->insert([
            'name' => 'David',
            'email' => 'davidalfredo.hdez@gmail.com',
            'password' => hash('sha256','secret_48'),
            'role' => 'ROLE_USER',
        ]);
    }
}
