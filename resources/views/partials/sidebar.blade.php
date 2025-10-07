<div class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <h3><i class="fas fa-graduation-cap"></i> School MS</h3>
        </div>

        <div class="sidebar-menu">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            @if(auth()->user()->isAdmin())
                <!-- Admin Menu -->
                <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                </a>

                <a href="{{ route('teachers.index') }}" class="{{ request()->routeIs('teachers.*') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Teachers</span>
                </a>

                <a href="{{ route('classes.index') }}" class="{{ request()->routeIs('classes.*') ? 'active' : '' }}">
                    <i class="fas fa-school"></i>
                    <span>Classes</span>
                </a>

                <a href="{{ route('subjects.index') }}" class="{{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Subjects</span>
                </a>

                <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Attendance</span>
                </a>

                <a href="{{ route('exams.index') }}" class="{{ request()->routeIs('exams.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Exams</span>
                </a>

                <a href="{{ route('announcements.index') }}" class="{{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>

                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>

            @elseif(auth()->user()->isTeacher())
                <!-- Teacher Menu -->
                <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                </a>

                <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Attendance</span>
                </a>

                <a href="{{ route('exams.index') }}" class="{{ request()->routeIs('exams.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    <span>Exams & Grades</span>
                </a>

                <a href="{{ route('announcements.index') }}" class="{{ request()->routeIs('announcements.*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>

                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>

            @elseif(auth()->user()->isStudent())
                <!-- Student Menu -->
                <a href="{{ route('announcements.public') }}" class="{{ request()->routeIs('announcements.public') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>

                <a href="{{ route('student.attendance', auth()->user()->student) }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>My Attendance</span>
                </a>

                <a href="{{ route('grades.studentReport') }}">
                    <i class="fas fa-graduation-cap"></i>
                    <span>My Grades</span>
                </a>

            @elseif(auth()->user()->isParent())
                <!-- Parent Menu -->
                <a href="{{ route('announcements.public') }}" class="{{ request()->routeIs('announcements.public') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
            @endif
        </div>
    </div>