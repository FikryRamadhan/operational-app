<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Man',
            'email' => 'Hil@gmail.com',
            'phone_number' => '172878',
            'password' => bcrypt('123'),
            'role' => 'Owner'
        ]);
        User::create([
            'name' => 'ina',
            'email' => 'ina@gmail.com',
            'phone_number' => '172878',
            'password' => bcrypt('123'),
            'role' => 'Staff'
        ]);
        User::create([
            'name' => 'iman',
            'email' => 'iman@gmail.com',
            'phone_number' => '172878',
            'password' => bcrypt('123'),
            'role' => 'Staff'
        ]);
    }
}
