@extends('layouts.app')

@section('title', 'Parent Dashboard')

@section('content')
<div class="mb-4">
    <h1 class="page-title">Parent Dashboard</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-4x text-primary mb-4"></i>
                <h3>Welcome to the Parent Portal</h3>
                <p class="text-muted">Stay updated with school announcements and your child's progress.</p>
            </div>
        </div>
    </div>
</div>

<!-- Announcements -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-bullhorn me-2"></i>Recent Announcements</span>
                <a href="{{ route('announcements.public') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @forelse($announcements as $announcement)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">{{ $announcement->title }}</h6>
                            <span class="badge bg-{{ $announcement->priority_badge_class }}">
                                {{ ucfirst($announcement->priority) }}
                            </span>
                        </div>
                        <p class="text-muted mb-2">{{ Str::limit($announcement->content, 150) }}</p>
                        <small class="text-muted">
                            <i class="far fa-clock me-1"></i>{{ $announcement->created_at->diffForHumans() }}
                        </small>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">No announcements</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection