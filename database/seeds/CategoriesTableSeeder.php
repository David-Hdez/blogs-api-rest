<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('categories')->insert([
            'name' => 'Android',            
        ]);

        DB::table('categories')->insert([
            'name' => 'Windows',            
        ]);

        DB::table('categories')->insert([
            'name' => 'Linux',            
        ]);
    }
}
