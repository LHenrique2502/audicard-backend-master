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
        DB::table('users')->insert([
            'uuid'=> '1221212',
            'name' => 'Desenvolvedor',
            'type' => 1,
            'email' => 'otonielloliveira@gmail.com',
            'password' => bcrypt('22039696'),
        ]);
    }
}
