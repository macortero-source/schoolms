@extends('layouts.app')

@section('title', 'Subjects')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Subjects Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Subjects</li>
            </ol>
        </nav>
    </div>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('subjects.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Subject
        </a>
    @endif
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('subjects.index') }}">
            <div class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Search subjects..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="core" {{ request('type') == 'core' ? 'selected' : '' }}>Core</option>
                        <option value="elective" {{ request('type') == 'elective' ? 'selected' : '' }}>Elective</option>
                        <option value="optional" {{ request('type') == 'optional' ? 'selected' : '' }}>Optional</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('subjects.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Subjects Grid -->
<div class="row">
    @forelse($subjects as $subject)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1">{{ $subject->name }}</h5>
                            <small class="text-muted">{{ $subject->code }}</small>
                        </div>
                        <div>
                            <span class="badge bg-{{ $subject->type == 'core' ? 'primary' : ($subject->type == 'elective' ? 'info' : 'secondary') }}">
                                {{ ucfirst($subject->type) }}
                            </span>
                        </div>
                    </div>

                    @if($subject->description)
                        <p class="text-muted small">{{ Str::limit($subject->description, 80) }}</p>
                    @endif

                    <div class="mb-3">
                        <i class="fas fa-book me-2 text-muted"></i>
                        <strong>{{ $subject->credit_hours }}</strong> Credit Hours
                    </div>

                    <div class="mb-3">
                        <i class="fas fa-users me-2 text-muted"></i>
                        <strong>{{ $subject->teachers->unique('id')->count() }}</strong> Teachers
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('subjects.show', $subject) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        @if(auth()->user()->isAdmin())
                            <div>
                                <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No subjects found</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($subjects->hasPages())
    <div class="mt-3">
        {{ $subjects->links() }}
    </div>
@endif
@endsection