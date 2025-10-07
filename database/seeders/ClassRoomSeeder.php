<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use Illuminate\Database\Seeder;

class ClassRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            // Junior Secondary School (JSS)
            ['name' => 'JSS 1', 'grade_level' => '7', 'section' => 'A', 'capacity' => 40, 'room_number' => 'R101'],
            ['name' => 'JSS 1', 'grade_level' => '7', 'section' => 'B', 'capacity' => 40, 'room_number' => 'R102'],
            ['name' => 'JSS 2', 'grade_level' => '8', 'section' => 'A', 'capacity' => 40, 'room_number' => 'R201'],
            ['name' => 'JSS 2', 'grade_level' => '8', 'section' => 'B', 'capacity' => 40, 'room_number' => 'R202'],
            ['name' => 'JSS 3', 'grade_level' => '9', 'section' => 'A', 'capacity' => 40, 'room_number' => 'R301'],
            ['name' => 'JSS 3', 'grade_level' => '9', 'section' => 'B', 'capacity' => 40, 'room_number' => 'R302'],
            
            // Senior Secondary School (SSS)
            ['name' => 'SSS 1', 'grade_level' => '10', 'section' => 'A', 'capacity' => 35, 'room_number' => 'R401'],
            ['name' => 'SSS 1', 'grade_level' => '10', 'section' => 'B', 'capacity' => 35, 'room_number' => 'R402'],
            ['name' => 'SSS 2', 'grade_level' => '11', 'section' => 'A', 'capacity' => 35, 'room_number' => 'R501'],
            ['name' => 'SSS 2', 'grade_level' => '11', 'section' => 'B', 'capacity' => 35, 'room_number' => 'R502'],
            ['name' => 'SSS 3', 'grade_level' => '12', 'section' => 'A', 'capacity' => 30, 'room_number' => 'R601'],
            ['name' => 'SSS 3', 'grade_level' => '12', 'section' => 'B', 'capacity' => 30, 'room_number' => 'R602'],
        ];

        foreach ($classes as $class) {
            ClassRoom::create([
                'name' => $class['name'],
                'grade_level' => $class['grade_level'],
                'section' => $class['section'],
                'capacity' => $class['capacity'],
                'room_number' => $class['room_number'],
                'description' => 'Standard classroom for ' . $class['name'] . ' Section ' . $class['section'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Classes seeded successfully!');
    }
}