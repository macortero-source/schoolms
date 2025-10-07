<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->teacher(),
            'employee_number' => 'EMP' . date('Y') . str_pad(fake()->unique()->numberBetween(1, 999), 4, '0', STR_PAD_LEFT),
            'qualification' => fake()->randomElement(['B.Ed', 'B.Sc', 'M.Ed', 'M.Sc', 'PhD']),
            'specialization' => fake()->randomElement(['Mathematics', 'Physics', 'Chemistry', 'Biology', 'English', 'History', 'Geography', 'Computer Science']),
            'salary' => fake()->randomFloat(2, 50000, 200000),
            'joining_date' => fake()->dateTimeBetween('-10 years', 'now'),
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-25 years'),
            'gender' => fake()->randomElement(['male', 'female']),
            'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'nationality' => 'Nigerian',
            'employment_type' => fake()->randomElement(['full-time', 'part-time', 'contract']),
            'experience' => fake()->sentence(),
            'certifications' => fake()->boolean(40) ? fake()->words(3, true) : null,
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'emergency_contact_relation' => fake()->randomElement(['Spouse', 'Parent', 'Sibling', 'Friend']),
            'remarks' => fake()->boolean(30) ? fake()->sentence() : null,
            'is_active' => true,
        ];
    }
}