{{-- resources/views/layouts/sidebar.blade.php --}}
<div class="list-group list-group-flush">
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" 
       class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt me-2"></i>
        Dashboard
    </a>
    
    @if(Auth::user()->isAdmin())
        <!-- Admin Menu -->
        <div class="list-group-item bg-light">
            <small class="text-muted fw-bold">ADMINISTRATION</small>
        </div>
        
        <a href="{{ route('users.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fas fa-users me-2"></i>
            User Management
        </a>
        
        <a href="{{ route('classes.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('classes.*') ? 'active' : '' }}">
            <i class="fas fa-school me-2"></i>
            Classes
        </a>
        
        <a href="{{ route('teachers.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
            <i class="fas fa-chalkboard-teacher me-2"></i>
            Teachers
        </a>
    @endif
    
    @if(Auth::user()->isAdmin() || Auth::user()->isTeacher())
        <!-- Academic Menu -->
        <div class="list-group-item bg-light">
            <small class="text-muted fw-bold">ACADEMIC</small>
        </div>
        
        <a href="{{ route('students.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('students.*') ? 'active' : '' }}">
            <i class="fas fa-user-graduate me-2"></i>
            Students
        </a>
        
        <a href="{{ route('attendance.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-check me-2"></i>
            Attendance
        </a>
        
        <a href="{{ route('exams.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('exams.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list me-2"></i>
            Exams & Grades
        </a>
        
        <a href="{{ route('announcements.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
            <i class="fas fa-bullhorn me-2"></i>
            Announcements
        </a>
    @endif
    
    @if(Auth::user()->isStudent() || Auth::user()->isParent())
        <!-- Student/Parent Menu -->
        <div class="list-group-item bg-light">
            <small class="text-muted fw-bold">MY PORTAL</small>
        </div>
        
        <a href="{{ route('student.attendance') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('student.attendance') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt me-2"></i>
            My Attendance
        </a>
        
        <a href="{{ route('student.grades') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('student.grades') ? 'active' : '' }}">
            <i class="fas fa-star me-2"></i>
            My Grades
        </a>
        
        <a href="{{ route('announcements.public') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('announcements.public') ? 'active' : '' }}">
            <i class="fas fa-bell me-2"></i>
            Announcements
        </a>
    @endif
    
    <!-- Common Menu -->
    <div class="list-group-item bg-light">
        <small class="text-muted fw-bold">TOOLS</small>
    </div>
    
    <a href="#" class="list-group-item list-group-item-action">
        <i class="fas fa-calendar me-2"></i>
        Calendar
    </a>
    
    <a href="#" class="list-group-item list-group-item-action">
        <i class="fas fa-chart-bar me-2"></i>
        Reports
    </a>
    
    <a href="#" class="list-group-item list-group-item-action">
        <i class="fas fa-cog me-2"></i>
        Settings
    </a>
</div>