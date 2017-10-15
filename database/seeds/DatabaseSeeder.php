<?php

use Illuminate\Database\Seeder;
use App\User;
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
		User::create([
            'first_name' => 'kamal',
            'last_name' => 'uddin',
            'email' => 'admin@example.com',
			'phone' => '01745519614',
			'gender'=>'male',
			'country'=>'Bangladesh',
			'address'=>'Dhaka, Bangladesh',
            'role' => '1',
			'status' => 'active',
            'password' => '123456'
            
        ]);
    }
}
