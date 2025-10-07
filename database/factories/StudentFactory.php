<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ClassRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'student_number' => 'STU' . date('Y') . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'class_id' => ClassRoom::inRandomOrder()->first()?->id,
            'admission_number' => 'ADM' . date('Y') . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'admission_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'academic_year' => date('Y') . '-' . (date('Y') + 1),
            'date_of_birth' => fake()->dateTimeBetween('-18 years', '-6 years'),
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
            'medical_conditions' => fake()->boolean(20) ? fake()->sentence() : null,
            'allergies' => fake()->boolean(15) ? fake()->words(3, true) : null,
            'previous_school' => fake()->boolean(60) ? fake()->company() . ' School' : null,
            'remarks' => fake()->boolean(30) ? fake()->sentence() : null,
            'is_active' => true,
        ];
    }
}