<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentUsers = User::where('role', 'student')->get();
        $classes = ClassRoom::all();

        if ($classes->isEmpty()) {
            $this->command->error('No classes found! Please run ClassRoomSeeder first.');
            return;
        }

        foreach ($studentUsers as $index => $user) {
            // Distribute students evenly across classes
            $assignedClass = $classes[$index % $classes->count()];

            Student::create([
                'user_id' => $user->id,
                'student_number' => 'STU' . date('Y') . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'class_id' => $assignedClass->id,
                'admission_number' => 'ADM' . date('Y') . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'admission_date' => Carbon::now()->subMonths(rand(1, 36)),
                'academic_year' => date('Y') . '-' . (date('Y') + 1),
                'date_of_birth' => Carbon::now()->subYears(rand(12, 18)),
                'gender' => fake()->randomElement(['male', 'female']),
                'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
                'nationality' => 'Nigerian',
                'religion' => fake()->randomElement(['Christianity', 'Islam', 'Traditional', 'Other']),
                'parent_name' => fake()->name(),
                'parent_phone' => fake()->phoneNumber(),
                'parent_email' => fake()->safeEmail(),
                'parent_occupation' => fake()->jobTitle(),
                'parent_address' => fake()->address(),
                'emergency_contact_name' => fake()->name(),
                'emergency_contact_phone' => fake()->phoneNumber(),
                'emergency_contact_relation' => fake()->randomElement(['Father', 'Mother', 'Guardian', 'Uncle', 'Aunt']),
                'medical_conditions' => fake()->boolean(15) ? fake()->randomElement(['Asthma', 'Allergies', 'Diabetes']) : null,
                'allergies' => fake()->boolean(10) ? fake()->randomElement(['Peanuts', 'Dust', 'Pollen']) : null,
                'previous_school' => fake()->boolean(70) ? fake()->company() . ' Secondary School' : null,
                'remarks' => fake()->boolean(20) ? 'Good academic performance' : null,
                'is_active' => true,
            ]);
        }

        $this->command->info('Students seeded successfully!');
        $this->command->info('Total students created: ' . Student::count());
    }
}