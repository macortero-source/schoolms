@extends('layouts.app')

@section('title', 'Classes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Classes Management</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Classes</li>
            </ol>
        </nav>
    </div>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('classes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Class
        </a>
    @endif
</div>

<!-- Classes Grid -->
<div class="row">
    @forelse($classes as $class)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="mb-0">{{ $class->full_name }}</h5>
                        <span class="badge bg-{{ $class->is_active ? 'success' : 'secondary' }}">
                            {{ $class->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Class Teacher</small>
                        <strong>{{ $class->class_teacher_name }}</strong>
                    </div>

                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <h4 class="mb-0">{{ $class->total_students }}</h4>
                            <small class="text-muted">Students</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-0">{{ $class->capacity }}</h4>
                            <small class="text-muted">Capacity</small>
                        </div>
                    </div>

                    @if($class->room_number)
                        <p class="text-muted mb-3"><i class="fas fa-door-open me-2"></i>Room: {{ $class->room_number }}</p>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('classes.show', $class) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        @if(auth()->user()->isAdmin())
                            <div>
                                <a href="{{ route('classes.edit', $class) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('classes.destroy', $class) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
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
                    <i class="fas fa-school fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No classes found</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($classes->hasPages())
    <div class="mt-3">
        {{ $classes->links() }}
    </div>
@endif
@endsection