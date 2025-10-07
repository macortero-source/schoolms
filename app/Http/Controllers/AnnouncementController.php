<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index(Request $request)
    {
        $query = Announcement::with('poster');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
        }

        // Filter by target audience
        if ($request->filled('target_audience')) {
            $query->where('target_audience', $request->target_audience);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status ==='published') {
$query->published();
} elseif ($request->status === 'expired') {
$query->expired();
} elseif ($request->status === 'scheduled') {
$query->scheduled();
}
}
    $announcements = $query->latest()->paginate(15);

    return view('announcements.index', compact('announcements'));
}

/**
 * Show the form for creating a new announcement.
 */
public function create()
{
    return view('announcements.create');
}

/**
 * Store a newly created announcement in storage.
 */
public function store(StoreAnnouncementRequest $request)
{
    try {
        $data = $request->validated();
        $data['posted_by'] = auth()->id();
        $data['is_active'] = true;

        Announcement::create($data);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement created successfully!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create announcement: ' . $e->getMessage());
    }
}

/**
 * Display the specified announcement.
 */
public function show(Announcement $announcement)
{
    $announcement->load('poster');

    return view('announcements.show', compact('announcement'));
}

/**
 * Show the form for editing the specified announcement.
 */
public function edit(Announcement $announcement)
{
    return view('announcements.edit', compact('announcement'));
}

/**
 * Update the specified announcement in storage.
 */
public function update(UpdateAnnouncementRequest $request, Announcement $announcement)
{
    try {
        $announcement->update($request->validated());

        return redirect()->route('announcements.show', $announcement)
            ->with('success', 'Announcement updated successfully!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update announcement: ' . $e->getMessage());
    }
}

/**
 * Remove the specified announcement from storage.
 */
public function destroy(Announcement $announcement)
{
    try {
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully!');

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Failed to delete announcement: ' . $e->getMessage());
    }
}

/**
 * Toggle announcement active status
 */
public function toggleStatus(Announcement $announcement)
{
    $announcement->update(['is_active' => !$announcement->is_active]);

    $status = $announcement->is_active ? 'activated' : 'deactivated';

    return redirect()->back()
        ->with('success', "Announcement {$status} successfully!");
}

/**
 * Show public announcements (for students/parents)
 */
public function public()
{
    $user = auth()->user();
    
    $announcements = Announcement::published()
        ->forAudience($user->role)
        ->latest()
        ->paginate(10);

    return view('announcements.public', compact('announcements'));
}
}
