<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Http\Requests\StoreAttendanceRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Display attendance management page
     */
    public function index(Request $request)
    {
        $classes = ClassRoom::active()->orderBy('name')->get();
        $selectedClass = null;
        $selectedDate = $request->date ?? Carbon::today()->format('Y-m-d');
        $students = collect();
        $attendance = collect();

        if ($request->filled('class_id')) {
            $selectedClass = ClassRoom::findOrFail($request->class_id);
            
            // Get students in the class
            $students = Student::where('class_id', $request->class_id)
                ->where('is_active', true)
                ->with('user')
                ->orderBy('created_at')
                ->get();

            // Get existing attendance for the date
            $attendance = Attendance::where('class_id', $request->class_id)
                ->whereDate('date', $selectedDate)
                ->get()
                ->keyBy('student_id');
        }

        return view('attendance.index', compact(
            'classes',
            'selectedClass',
            'selectedDate',
            'students',
            'attendance'
        ));
    }

    /**
     * Store attendance records
     */
    public function store(StoreAttendanceRequest $request)
    {
        try {
            DB::beginTransaction();

            $classId = $request->class_id;
            $date = $request->date;

            // Delete existing attendance for this class and date
            Attendance::where('class_id', $classId)
                ->whereDate('date', $date)
                ->delete();

            // Insert new attendance records
            $attendanceData = [];
            foreach ($request->attendance as $record) {
                $attendanceData[] = [
                    'student_id' => $record['student_id'],
                    'class_id' => $classId,
                    'date' => $date,
                    'status' => $record['status'],
                    'remarks' => $record['remarks'] ?? null,
                    'marked_by' => auth()->id(),
                    'check_in_time' => $record['status'] === 'present' ? '08:00:00' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Attendance::insert($attendanceData);

            DB::commit();

            return redirect()->route('attendance.index', [
                'class_id' => $classId,
                'date' => $date
            ])->with('success', 'Attendance marked successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to mark attendance: ' . $e->getMessage());
        }
    }

    /**
     * Show attendance report
     */
    public function report(Request $request)
    {
        $classes = ClassRoom::active()->orderBy('name')->get();
        $students = collect();
        $attendanceData = [];
        $selectedClass = null;
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        if ($request->filled('class_id')) {
            $selectedClass = ClassRoom::findOrFail($request->class_id);
            
            $students = Student::where('class_id', $request->class_id)
                ->where('is_active', true)
                ->with('user')
                ->get();

            foreach ($students as $student) {
                $attendanceData[$student->id] = [
                    'student' => $student,
                    'total_days' => Attendance::where('student_id', $student->id)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->count(),
                    'present' => Attendance::where('student_id', $student->id)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->where('status', 'present')
                        ->count(),
                    'absent' => Attendance::where('student_id', $student->id)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->where('status', 'absent')
                        ->count(),
                    'late' => Attendance::where('student_id', $student->id)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->where('status', 'late')
                        ->count(),
                    'percentage' => $student->getAttendancePercentage($startDate, $endDate),
                ];
            }
        }

        return view('attendance.report', compact(
            'classes',
            'selectedClass',
            'attendanceData',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show student attendance details
     */
    public function student(Student $student, Request $request)
{
    // Load related user and class so Blade can access them
    $student->load('user', 'class');

    $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
    $endDate   = $request->end_date ?? Carbon::now();

    $attendance = $student->attendances()
        ->whereBetween('date', [$startDate, $endDate])
        ->with('markedByTeacher')
        ->orderBy('date', 'desc')
        ->paginate(20);

    $stats = [
        'total_days' => $attendance->total(),
        'present'    => $student->getTotalPresentDays($startDate, $endDate),
        'absent'     => $student->getTotalAbsentDays($startDate, $endDate),
        'percentage' => $student->getAttendancePercentage($startDate, $endDate),
    ];

    return view('attendance.student', compact('student', 'attendance', 'stats', 'startDate', 'endDate'));
}


    /**
     * Export attendance report
     */
    public function export(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $class = ClassRoom::findOrFail($request->class_id);
        $students = Student::where('class_id', $request->class_id)
            ->where('is_active', true)
            ->with('user')
            ->get();

        $filename = 'attendance_' . $class->name . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($students, $request) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Student Number', 'Name', 'Total Days', 'Present', 'Absent', 
                'Late', 'Attendance %'
            ]);

            // Data
            foreach ($students as $student) {
                $totalDays = Attendance::where('student_id', $student->id)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->count();
                
                $present = Attendance::where('student_id', $student->id)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->where('status', 'present')
                    ->count();
                
                $absent = Attendance::where('student_id', $student->id)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->where('status', 'absent')
                    ->count();
                
                $late = Attendance::where('student_id', $student->id)
                    ->whereBetween('date', [$request->start_date, $request->end_date])
                    ->where('status', 'late')
                    ->count();

                $percentage = $totalDays > 0 ? round(($present / $totalDays) * 100, 2) : 0;

                fputcsv($file, [
                    $student->student_number,
                    $student->user->name,
                    $totalDays,
                    $present,
                    $absent,
                    $late,
                    $percentage . '%',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}