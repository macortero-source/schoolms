<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Admin Dashboard</h1>
  <p>Welcome, {{ Auth::user()->name }}</p>
</div>
@endsection