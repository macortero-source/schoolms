{{-- resources/views/announcements/public.blade.php --}}
@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
    <!-- Hero Section -->
    <div class="hero-section" style="min-height: 40vh;">
        <div class="hero-content">
            <h1>Announcements</h1>
            <p>Stay updated with the latest news and events</p>
        </div>
        <i class="fas fa-bullhorn background-icon"></i>
    </div>

    <!-- Announcements List -->
    <div class="container py-5">
        <div class="row">
            @forelse($announcements as $announcement)
                <div class="col-md-6 mb-4">
                    <div class="feature-card h-100">
                        <h4 class="mb-2">{{ $announcement->title }}</h4>
                        <p class="text-muted mb-3">
                            {{ Str::limit($announcement->content, 180) }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="far fa-clock me-1"></i>
                                {{ $announcement->created_at->diffForHumans() }}
                            </small>
                            <span class="badge bg-{{ $announcement->priority_badge_class }}">
                                {{ ucfirst($announcement->priority) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">No announcements available.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $announcements->links() }}
        </div>
    </div>
@endsection
