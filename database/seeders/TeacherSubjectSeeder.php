<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\Subject;
use App\Models\ClassRoom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = Teacher::all();
        $subjects = Subject::all();
        $classes = ClassRoom::all();
        $academicYear = date('Y') . '-' . (date('Y') + 1);

        if ($teachers->isEmpty() || $subjects->isEmpty() || $classes->isEmpty()) {
            $this->command->error('Please seed Teachers, Subjects, and Classes first!');
            return;
        }

        // Assign class teachers
        foreach ($classes as $index => $class) {
            $teacher = $teachers[$index % $teachers->count()];
            $class->update(['class_teacher_id' => $teacher->user_id]);
        }

        $this->command->info('Class teachers assigned!');

        // Assign subjects to teachers for each class
        $assignments = [];
        
        foreach ($classes as $class) {
            // Each class should have 6-8 subjects
            $classSubjects = $subjects->random(min(8, $subjects->count()));
            
            foreach ($classSubjects as $subject) {
                // Find a teacher with matching specialization or random
                $teacher = $teachers->firstWhere('specialization', $subject->name) 
                    ?? $teachers->random();

                // Check if this combination already exists
                $exists = DB::table('teacher_subject')
                    ->where('teacher_id', $teacher->id)
                    ->where('subject_id', $subject->id)
                    ->where('class_id', $class->id)
                    ->where('academic_year', $academicYear)
                    ->exists();

                if (!$exists) {
                    $assignments[] = [
                        'teacher_id' => $teacher->id,
                        'subject_id' => $subject->id,
                        'class_id' => $class->id,
                        'academic_year' => $academicYear,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert all assignments
        DB::table('teacher_subject')->insert($assignments);

        $this->command->info('Teacher-Subject assignments created!');
        $this->command->info('Total assignments: ' . count($assignments));
    }
}