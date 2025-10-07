<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            // Core Subjects
            ['name' => 'Mathematics', 'code' => 'MTH101', 'type' => 'core', 'credit_hours' => 4, 'description' => 'Basic and Advanced Mathematics'],
            ['name' => 'English Language', 'code' => 'ENG101', 'type' => 'core', 'credit_hours' => 4, 'description' => 'Grammar, Literature, and Composition'],
            ['name' => 'Physics', 'code' => 'PHY101', 'type' => 'core', 'credit_hours' => 3, 'description' => 'Mechanics, Electricity, and Waves'],
            ['name' => 'Chemistry', 'code' => 'CHM101', 'type' => 'core', 'credit_hours' => 3, 'description' => 'Organic and Inorganic Chemistry'],
            ['name' => 'Biology', 'code' => 'BIO101', 'type' => 'core', 'credit_hours' => 3, 'description' => 'Life Sciences and Ecology'],
            
            // Elective Subjects
            ['name' => 'Computer Science', 'code' => 'CSC101', 'type' => 'elective', 'credit_hours' => 3, 'description' => 'Programming and Information Technology'],
            ['name' => 'Economics', 'code' => 'ECO101', 'type' => 'elective', 'credit_hours' => 3, 'description' => 'Micro and Macro Economics'],
            ['name' => 'Geography', 'code' => 'GEO101', 'type' => 'elective', 'credit_hours' => 3, 'description' => 'Physical and Human Geography'],
            ['name' => 'History', 'code' => 'HIS101', 'type' => 'elective', 'credit_hours' => 2, 'description' => 'World and African History'],
            ['name' => 'Literature', 'code' => 'LIT101', 'type' => 'elective', 'credit_hours' => 2, 'description' => 'Poetry, Prose, and Drama'],
            
            // Optional Subjects
            ['name' => 'Agricultural Science', 'code' => 'AGR101', 'type' => 'optional', 'credit_hours' => 2, 'description' => 'Farming and Animal Husbandry'],
            ['name' => 'Fine Arts', 'code' => 'ART101', 'type' => 'optional', 'credit_hours' => 2, 'description' => 'Drawing, Painting, and Design'],
            ['name' => 'Music', 'code' => 'MUS101', 'type' => 'optional', 'credit_hours' => 2, 'description' => 'Theory and Practical Music'],
            ['name' => 'Physical Education', 'code' => 'PED101', 'type' => 'optional', 'credit_hours' => 2, 'description' => 'Sports and Fitness'],
            ['name' => 'French Language', 'code' => 'FRE101', 'type' => 'optional', 'credit_hours' => 2, 'description' => 'Basic French Communication'],
            ['name' => 'Religious Studies', 'code' => 'CRS101', 'type' => 'optional', 'credit_hours' => 2, 'description' => 'Christian Religious Studies'],
            ['name' => 'Civic Education', 'code' => 'CIV101', 'type' => 'optional', 'credit_hours' => 2, 'description' => 'Government and Citizenship'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        $this->command->info('Subjects seeded successfully!');
    }
}