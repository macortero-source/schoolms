<!-- resources/views/partials/navbar.blade.php -->
<div class="top-navbar d-flex justify-content-between align-items-center">
    <div>
        <button class="btn btn-link d-md-none" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <span class="ms-2">{{ greetUser() }}, <strong>{{ auth()->user()->name }}</strong></span>
    </div>

    <div class="dropdown profile-dropdown">
        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <x-user-avatar :user="auth()->user()" :size="35" class="rounded-circle" />
            <span class="ms-2 d-none d-md-inline">{{ auth()->user()->name ?? 'User' }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="fas fa-user me-2"></i> Profile
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>

