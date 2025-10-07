<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::where('is_active', true)->get();
        $teachers = User::where('role', 'teacher')->pluck('id');

        if ($students->isEmpty() || $teachers->isEmpty()) {
            $this->command->error('Please seed Students and Teachers first!');
            return;
        }

        // Generate attendance for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        $attendanceRecords = [];

        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($students as $student) {
                // 85% chance of being present, 10% absent, 5% late
                $random = rand(1, 100);
                
                if ($random <= 85) {
                    $status = 'present';
                } elseif ($random <= 95) {
                    $status = 'absent';
                } else {
                    $status = 'late';
                }

                $attendanceRecords[] = [
                    'student_id' => $student->id,
                    'class_id' => $student->class_id,
                    'date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'marked_by' => $teachers->random(),
                    'remarks' => $status === 'absent' ? 'No reason provided' : null,
                    'check_in_time' => $status === 'present' ? '08:00:00' : ($status === 'late' ? '08:30:00' : null),
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
        }

        // Insert in chunks to avoid memory issues
        foreach (array_chunk($attendanceRecords, 500) as $chunk) {
            Attendance::insert($chunk);
        }

        $this->command->info('Attendance records created!');
        $this->command->info('Total attendance records: ' . count($attendanceRecords));
    }
}