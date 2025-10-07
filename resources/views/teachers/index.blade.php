@extends('layouts.app')

@section('title', 'Teachers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Teachers Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Teachers</li>
            </ol>
        </nav>
    </div>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('teachers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Teacher
        </a>
    @endif
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('teachers.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email, employee #..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="specialization" class="form-select">
                        <option value="">All Specializations</option>
                        <option value="Mathematics" {{ request('specialization') == 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                        <option value="English Language" {{ request('specialization') == 'English Language' ? 'selected' : '' }}>English Language</option>
                        <option value="Physics" {{ request('specialization') == 'Physics' ? 'selected' : '' }}>Physics</option>
                        <option value="Chemistry" {{ request('specialization') == 'Chemistry' ? 'selected' : '' }}>Chemistry</option>
                        <option value="Biology" {{ request('specialization') == 'Biology' ? 'selected' : '' }}>Biology</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="employment_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="full-time" {{ request('employment_type') == 'full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="part-time" {{ request('employment_type') == 'part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="contract" {{ request('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('teachers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Teachers Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2"></i>Teachers List ({{ $teachers->total() }})
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Employee #</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Specialization</th>
                        <th>Employment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td>
                                <img src="{{ $teacher->user->profile_photo ? asset('storage/' . $teacher->user->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($teacher->user->name) }}" 
                                     alt="{{ $teacher->user->name }}"
                                     class="rounded-circle"
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            </td>
                            <td><strong>{{ $teacher->employee_number }}</strong></td>
                            <td>{{ $teacher->user->name }}</td>
                            <td>{{ $teacher->user->email }}</td>
                            <td>{{ $teacher->specialization ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ ucfirst($teacher->employment_type) }}</span></td>
                            <td>
                                <span class="badge bg-{{ $teacher->is_active ? 'success' : 'danger' }}">
                                    {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No teachers found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($teachers->hasPages())
        <div class="card-footer">
            {{ $teachers->links() }}
        </div>
    @endif
</div>
@endsection