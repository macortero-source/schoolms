<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin Users
        $admin1 = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+234-800-000-0001',
            'address' => '1 School Admin Building, Lagos, Nigeria',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $admin2 = User::create([
            'name' => 'John Okafor',
            'email' => 'john.admin@school.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+234-800-000-0002',
            'address' => '2 Admin Block, Lagos, Nigeria',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin users created!');
        $this->command->info('Email: admin@school.com | Password: password');

        // Create Teacher Users
        $teachers = [
            ['name' => 'Dr. Sarah Johnson', 'email' => 'sarah.teacher@school.com', 'phone' => '+234-800-111-0001'],
            ['name' => 'Mr. Michael Chen', 'email' => 'michael.teacher@school.com', 'phone' => '+234-800-111-0002'],
            ['name' => 'Mrs. Amina Ibrahim', 'email' => 'amina.teacher@school.com', 'phone' => '+234-800-111-0003'],
            ['name' => 'Prof. David Williams', 'email' => 'david.teacher@school.com', 'phone' => '+234-800-111-0004'],
            ['name' => 'Ms. Grace Adeyemi', 'email' => 'grace.teacher@school.com', 'phone' => '+234-800-111-0005'],
            ['name' => 'Mr. James Okonkwo', 'email' => 'james.teacher@school.com', 'phone' => '+234-800-111-0006'],
            ['name' => 'Mrs. Elizabeth Brown', 'email' => 'elizabeth.teacher@school.com', 'phone' => '+234-800-111-0007'],
            ['name' => 'Dr. Ahmed Mohammed', 'email' => 'ahmed.teacher@school.com', 'phone' => '+234-800-111-0008'],
            ['name' => 'Ms. Rachel Nwosu', 'email' => 'rachel.teacher@school.com', 'phone' => '+234-800-111-0009'],
            ['name' => 'Mr. Peter Eze', 'email' => 'peter.teacher@school.com', 'phone' => '+234-800-111-0010'],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'phone' => $teacher['phone'],
                'address' => fake()->address(),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Teacher users created! (Password: password for all)');

        // Create Student Users (50 students)
        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'name' => fake()->name(),
                'email' => 'student' . $i . '@school.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'phone' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('50 Student users created! (student1@school.com to student50@school.com)');

        // Create Parent Users
        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'name' => fake()->name(),
                'email' => 'parent' . $i . '@school.com',
                'password' => Hash::make('password'),
                'role' => 'parent',
                'phone' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('20 Parent users created!');
        $this->command->info('Total users created: ' . User::count());
    }
}