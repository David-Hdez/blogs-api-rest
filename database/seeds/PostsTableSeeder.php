<?php

use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('posts')->insert([
            'user_id' => 2,            
            'category_id' => 1,            
            'title' => 'Android 12',            
            'content' => 'Nueva version del sistema',            
        ]);

        DB::table('posts')->insert([
            'user_id' => 2,            
            'category_id' => 2,            
            'title' => 'Gaming',            
            'content' => 'lorem ipsum',            
        ]);
    }
}
