<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->newLine();

        // Order is important due to foreign key constraints
        $this->call([
            UserSeeder::class,           // 1. Create users first
            ClassRoomSeeder::class,      // 2. Create classes
            SubjectSeeder::class,        // 3. Create subjects
            TeacherSeeder::class,        // 4. Create teacher profiles
            StudentSeeder::class,        // 5. Create student profiles
            TeacherSubjectSeeder::class, // 6. Assign teachers to subjects and classes
            AttendanceSeeder::class,     // 7. Generate attendance records
            ExamSeeder::class,           // 8. Create exams
            GradeSeeder::class,          // 9. Enter grades
            AnnouncementSeeder::class,   // 10. Create announcements
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('📧 Login Credentials:');
        $this->command->info('================================');
        $this->command->info('Admin: admin@school.com | Password: password');
        $this->command->info('Teachers: sarah.teacher@school.com (and others) | Password: password');
        $this->command->info('Students: student1@school.com to student50@school.com | Password: password');
        $this->command->info('Parents: parent1@school.com to parent20@school.com | Password: password');
        $this->command->info('================================');
    }
}