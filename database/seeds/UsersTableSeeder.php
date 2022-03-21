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
        \App\User::create([
            'name' => 'Reynard Nathanael',
            'username' => 'reynardnath',
            'password' => bcrypt('password'),
            'email' => 'reynardnath@gmail.com',
        ]);
    }
}
