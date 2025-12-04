@extends('layouts.app')

@section('content')
    <h1>{{ $exam->name }}</h1>
    <p>Class: {{ $exam->class->name }}</p>
    <p>Subject: {{ $exam->subject->name }}</p>

    <h2>Statistics</h2>
    <ul>
        <li>Total students: {{ $stats['total_students'] }}</li>
        <li>Graded students: {{ $stats['graded_students'] }}</li>
        <li>Pending grades: {{ $stats['pending_grades'] }}</li>
        <li>Average marks: {{ $stats['average_marks'] }}</li>
        <li>Highest marks: {{ $stats['highest_marks'] }}</li>
        <li>Lowest marks: {{ $stats['lowest_marks'] }}</li>
        <li>Pass count: {{ $stats['pass_count'] }}</li>
        <li>Fail count: {{ $stats['fail_count'] }}</li>
        <li>Pass %: {{ $stats['pass_percentage'] }}%</li>
    </ul>

    <h2>Top Performers</h2>
    <ul>
        @foreach($topPerformers as $student)
            <li>{{ $student->student->user->name }} - {{ $student->marks_obtained }}</li>
        @endforeach
    </ul>
@endsection
