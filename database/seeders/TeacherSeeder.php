<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherUsers = User::where('role', 'teacher')->get();

        $qualifications = ['B.Ed', 'B.Sc', 'M.Ed', 'M.Sc', 'PhD'];
        $specializations = [
            'Mathematics',
            'English Language',
            'Physics',
            'Chemistry',
            'Biology',
            'Computer Science',
            'Economics',
            'Geography',
            'History',
            'Literature'
        ];
        $employmentTypes = ['full-time', 'part-time', 'contract'];

        foreach ($teacherUsers as $index => $user) {
            Teacher::create([
                'user_id' => $user->id,
                'employee_number' => 'EMP' . date('Y') . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'qualification' => $qualifications[array_rand($qualifications)],
                'specialization' => $specializations[$index % count($specializations)],
                'salary' => rand(80000, 250000),
                'joining_date' => Carbon::now()->subYears(rand(1, 10))->subMonths(rand(0, 11)),
                'date_of_birth' => Carbon::now()->subYears(rand(28, 55)),
                'gender' => fake()->randomElement(['male', 'female']),
                'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
                'nationality' => 'Nigerian',
                'employment_type' => $employmentTypes[array_rand($employmentTypes)],
                'experience' => rand(2, 15) . ' years of teaching experience',
                'certifications' => fake()->boolean(50) ? 'TRCN, Professional Certificate in Education' : null,
                'emergency_contact_name' => fake()->name(),
                'emergency_contact_phone' => fake()->phoneNumber(),
                'emergency_contact_relation' => fake()->randomElement(['Spouse', 'Parent', 'Sibling']),
                'remarks' => fake()->boolean(30) ? 'Excellent performance record' : null,
                'is_active' => true,
            ]);
        }

        $this->command->info('Teachers seeded successfully!');
    }
}