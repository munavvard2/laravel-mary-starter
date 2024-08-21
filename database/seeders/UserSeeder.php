<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Munavvarhushen Durvesh',
            'email' => 'munavvard222@gmail.com',
            'password' => bcrypt('password'),
            'is_customer' => false,
            'site' => 'site',
            'phone_number' => '9099103105'
        ]);
        User::create([
            'name' => 'Imarat',
            'email' => 'imarat@csdev.com',
            'password' => bcrypt('password'),
            'is_customer' => false,
            'site' => 'site',
            'phone_number' => '9099620620'
        ]);
        User::create([
            'name' => 'Customer 1',
            'email' => 'customer1@csdev.com',
            'password' => bcrypt('password'),
            'is_customer' => true,
            'site' => 'site1',
            'phone_number' => '9099103105'
        ]);
        User::create([
            'name' => 'Customer 2',
            'email' => 'customer2@csdev.com',
            'password' => bcrypt('password'),
            'is_customer' => true,
            'site' => 'site2',
            'phone_number' => '9099103105'
        ]);
    }
}
