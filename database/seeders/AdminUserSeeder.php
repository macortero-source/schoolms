<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'), // change to something secure
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
