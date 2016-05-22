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
        factory(Nht\User::class)->create([
            'email' => 'admin@finman.com',
            'password' => bcrypt('admin123123')
        ]);
        // $this->call(UsersTableSeeder::class);
    }
}
