<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = Subject::all();
        $classes = ClassRoom::all();
        $teachers = User::where('role', 'teacher')->pluck('id');
        $academicYear = date('Y') . '-' . (date('Y') + 1);

        if ($subjects->isEmpty() || $classes->isEmpty() || $teachers->isEmpty()) {
            $this->command->error('Please seed Subjects, Classes, and Teachers first!');
            return;
        }

        $examTypes = [
            ['type' => 'quiz', 'total' => 20, 'passing' => 10],
            ['type' => 'midterm', 'total' => 50, 'passing' => 25],
            ['type' => 'final', 'total' => 100, 'passing' => 40],
            ['type' => 'assignment', 'total' => 30, 'passing' => 15],
        ];

        $exams = [];

        foreach ($classes as $class) {
            // Get subjects for this class (from teacher_subject pivot)
            $classSubjects = Subject::whereHas('teachers', function($query) use ($class) {
                $query->where('teacher_subject.class_id', $class->id);
            })->get();

            foreach ($classSubjects as $subject) {
                foreach ($examTypes as $examType) {
                    // Create exams at different dates
                    $examDate = match($examType['type']) {
                        'quiz' => Carbon::now()->subDays(rand(10, 40)),
                        'midterm' => Carbon::now()->subDays(rand(5, 20)),
                        'assignment' => Carbon::now()->subDays(rand(15, 30)),
                        'final' => Carbon::now()->addDays(rand(5, 15)),
                    };

                    $exams[] = [
                        'name' => ucfirst($examType['type']) . ' - ' . $subject->name,
                        'subject_id' => $subject->id,
                        'class_id' => $class->id,
                        'exam_type' => $examType['type'],
                        'exam_date' => $examDate,
                        'start_time' => '09:00:00',
                        'end_time' => '11:00:00',
                        'duration_minutes' => 120,
                        'total_marks' => $examType['total'],
                        'passing_marks' => $examType['passing'],
                        'academic_year' => $academicYear,
                        'semester' => $examDate->month <= 6 ? 'Spring' : 'Fall',
                        'description' => ucfirst($examType['type']) . ' examination for ' . $subject->name,
                        'instructions' => 'Answer all questions. No external materials allowed.',
                        'room_number' => $class->room_number,
                        'created_by' => $teachers->random(),
                        'is_published' => $examDate->isPast(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert in chunks
        foreach (array_chunk($exams, 100) as $chunk) {
            Exam::insert($chunk);
        }

        $this->command->info('Exams created successfully!');
        $this->command->info('Total exams: ' . count($exams));
    }
}