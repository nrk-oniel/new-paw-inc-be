<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::create([
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'),
            'role_id' => Role::ROLE_ADMIN,
        ]);
        User::create([
            'username' => 'clinic',
            'email' => 'staff@gmail.com',
            'password' => bcrypt('clinic'),
            'role_id' => Role::ROLE_STAFF,
            'clinic_id' => 1,
        ]);
        User::create([
            'username' => 'customer',
            'email' => 'customer@gmail.com',
            'password' => bcrypt('customer'),
            'role_id' => Role::ROLE_CUSTOMER,
        ]);
    }
}
