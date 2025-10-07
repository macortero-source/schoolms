@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Announcements</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Announcements</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('announcements.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create Announcement
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('announcements.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search announcements..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="target_audience" class="form-select">
                        <option value="">All Audiences</option>
                        <option value="all" {{ request('target_audience') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="students" {{ request('target_audience') == 'students' ? 'selected' : '' }}>Students</option>
                        <option value="teachers" {{ request('target_audience') == 'teachers' ? 'selected' : '' }}>Teachers</option>
                        <option value="parents" {{ request('target_audience') == 'parents' ? 'selected' : '' }}>Parents</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="priority" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('announcements.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Announcements List -->
<div class="row">
    @forelse($announcements as $announcement)
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <h5 class="mb-2">
                                <a href="{{ route('announcements.show', $announcement) }}" class="text-decoration-none text-dark">
                                    {{ $announcement->title }}
                                </a>
                            </h5>
                            <p class="text-muted mb-2">{{ Str::limit($announcement->content, 200) }}</p>
                            <div>
                                <span class="badge bg-{{ $announcement->priority_badge_class }} me-2">
                                    {{ ucfirst($announcement->priority) }}
                                </span>
                                <span class="badge bg-{{ $announcement->target_audience_badge_class }} me-2">
                                    {{ ucfirst($announcement->target_audience) }}
                                </span>
                                @if($announcement->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                                @if($announcement->isExpired())
                                    <span class="badge bg-danger">Expired</span>
                                @elseif($announcement->isScheduled())
                                    <span class="badge bg-info">Scheduled</span>
                                @endif
                            </div>
                        </div>
                        <div class="btn-group ms-3" role="group">
                            <a href="{{ route('announcements.show', $announcement) }}" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('announcements.edit', $announcement) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>Posted by {{ $announcement->poster->name }}
                            <i class="far fa-clock ms-3 me-1"></i>{{ $announcement->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No announcements found</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

@if($announcements->hasPages())
    <div class="mt-3">
        {{ $announcements->links() }}
    </div>
@endif
@endsection