<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'user_name'=> 'Admin',
            'email'=> 'admin@gmail.com',
            'password'=>Hash::make('Admin@123'),
            'role'=> 'admin',
            'is_verified'=>1,
        ]);
        $admin->assignRole('super_admin');
    }
}
