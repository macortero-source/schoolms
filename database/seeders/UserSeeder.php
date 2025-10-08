<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $now = now();

        // -----------------------
        // Admin Users
        // -----------------------
        $admins = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@school.com',
                'phone' => '+234-800-000-0001',
                'address' => '1 School Admin Building, Lagos, Nigeria',
            ],
            [
                'name' => 'John Okafor',
                'email' => 'john.admin@school.com',
                'phone' => '+234-800-000-0002',
                'address' => '2 Admin Block, Lagos, Nigeria',
            ]
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'phone' => $admin['phone'],
                    'address' => $admin['address'],
                    'is_active' => true,
                    'email_verified_at' => $now,
                ]
            );
        }

        // -----------------------
        // Teacher Users
        // -----------------------
        $namedTeachers = [
            ['name' => 'Dr. Sarah Johnson', 'email' => 'sarah.teacher@school.com', 'phone' => '+234-800-111-0001'],
            ['name' => 'Mr. Michael Chen', 'email' => 'michael.teacher@school.com', 'phone' => '+234-800-111-0002'],
            ['name' => 'Mrs. Amina Ibrahim', 'email' => 'amina.teacher@school.com', 'phone' => '+234-800-111-0003'],
        ];

        foreach ($namedTeachers as $teacher) {
            User::updateOrCreate(
                ['email' => $teacher['email']],
                [
                    'name' => $teacher['name'],
                    'password' => Hash::make('password'),
                    'role' => 'teacher',
                    'phone' => $teacher['phone'],
                    'address' => $faker->address(),
                    'is_active' => true,
                    'email_verified_at' => $now,
                ]
            );
        }

        // Additional generic teachers
        for ($i = 1; $i <= 5; $i++) {
            User::updateOrCreate(
                ['email' => "teacher{$i}@school.com"],
                [
                    'name' => "Teacher {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'teacher',
                    'phone' => $faker->phoneNumber(),
                    'address' => $faker->address(),
                    'is_active' => true,
                    'email_verified_at' => $now,
                ]
            );
        }

        // -----------------------
        // Student Users
        // -----------------------
        for ($i = 1; $i <= 50; $i++) {
            User::updateOrCreate(
                ['email' => "student{$i}@school.com"],
                [
                    'name' => $faker->name(),
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'phone' => $faker->phoneNumber(),
                    'address' => $faker->address(),
                    'is_active' => true,
                    'email_verified_at' => $now,
                ]
            );
        }

        // -----------------------
        // Parent Users
        // -----------------------
        for ($i = 1; $i <= 20; $i++) {
            User::updateOrCreate(
                ['email' => "parent{$i}@school.com"],
                [
                    'name' => $faker->name(),
                    'password' => Hash::make('password'),
                    'role' => 'parent',
                    'phone' => $faker->phoneNumber(),
                    'address' => $faker->address(),
                    'is_active' => true,
                    'email_verified_at' => $now,
                ]
            );
        }

        $this->command->info('UserSeeder: Admins, teachers, students, and parents created successfully.');
    }
}
