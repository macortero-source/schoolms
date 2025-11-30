@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="mb-4">
    <h1 class="page-title">School Announcements</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Announcements</li>
        </ol>
    </nav>
</div>

<!-- Announcements List -->
<div class="row">
    @forelse($announcements as $announcement)
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <h5 class="mb-0 me-3">{{ $announcement->title }}</h5>
                                <span class="badge bg-{{ $announcement->priority_badge_class }}">
                                    {{ ucfirst($announcement->priority) }}
                                </span>
                            </div>
                            <p class="text-muted mb-3">{{ $announcement->content }}</p>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="fas fa-user me-2"></i>
                                <span>Posted by {{ $announcement->poster->name }}</span>
                                <i class="far fa-clock ms-3 me-2"></i>
                                <span>{{ $announcement->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                    <h5>No Announcements</h5>
                    <p class="text-muted">There are no announcements at this time.</p>
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