<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Exam;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only grade past exams
        $exams = Exam::where('exam_date', '<=', now())->get();
        $teachers = User::where('role', 'teacher')->pluck('id');

        if ($exams->isEmpty()) {
            $this->command->error('No past exams found!');
            return;
        }

        $grades = [];
        $gradeCount = 0;

        foreach ($exams as $exam) {
            // Get students in this exam's class
            $students = Student::where('class_id', $exam->class_id)
                ->where('is_active', true)
                ->get();

            foreach ($students as $student) {
                // 90% chance student took the exam
                if (rand(1, 100) <= 90) {
                    // Generate marks (normal distribution around 60-75%)
                    $percentage = $this->generateNormalDistribution(60, 75, 10);
                    $marksObtained = round(($percentage / 100) * $exam->total_marks, 2);

                    // Ensure marks don't exceed total
                    $marksObtained = min($marksObtained, $exam->total_marks);

                    $grades[] = [
                        'student_id' => $student->id,
                        'exam_id' => $exam->id,
                        'marks_obtained' => $marksObtained,
                        'entered_by' => $teachers->random(),
                        'entered_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $gradeCount++;
                } else {
                    // Student was absent
                    $grades[] = [
                        'student_id' => $student->id,
                        'exam_id' => $exam->id,
                        'marks_obtained' => 0,
                        'grade' => 'F',
                        'grade_point' => 0.00,
                        'status' => 'absent',
                        'remarks' => 'Absent from exam',
                        'entered_by' => $teachers->random(),
                        'entered_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $gradeCount++;
                }
            }
        }

        // Insert in chunks
        foreach (array_chunk($grades, 500) as $chunk) {
            Grade::insert($chunk);
        }

        $this->command->info('Grades created successfully!');
        $this->command->info('Total grades: ' . $gradeCount);
    }

    /**
     * Generate a random number with normal distribution
     */
    private function generateNormalDistribution($min, $max, $stdDev): float
    {
        $mean = ($min + $max) / 2;
        
        // Box-Muller transform for normal distribution
        $u1 = mt_rand() / mt_getrandmax();
        $u2 = mt_rand() / mt_getrandmax();
        $z = sqrt(-2 * log($u1)) * cos(2 * pi() * $u2);
        
        $value = $mean + $z * $stdDev;
        
        // Clamp to min-max range
        return max($min, min($max, $value));
    }
}