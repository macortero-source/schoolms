<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'School Management System') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --sidebar-width: 260px;
        }

        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-logo {
            padding: 1.5rem;
            background: rgba(255,255,255,0.1);
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo h3 {
            color: white;
            margin: 0;
            font-weight: 600;
            font-size: 1.3rem;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
        }

        .sidebar-menu a i {
            width: 30px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
        }

        .top-navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .top-navbar span {
  font-size: 1rem;
  color: #444;
}

.top-navbar strong {
  color: var(--primary-color);
}
.profile-dropdown .dropdown-toggle {
  color: #333;
  font-weight: 500;
}

.profile-dropdown .dropdown-menu {
  border-radius: 10px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  padding: 0.5rem 0;
}

.profile-dropdown .dropdown-item {
  display: flex;
  align-items: center;
  font-size: 0.9rem;
  padding: 0.6rem 1rem;
}

.profile-dropdown .dropdown-item i {
  width: 18px;
  text-align: center;
}


        .content-wrapper {
            padding: 2rem;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: white;
            border-bottom: 2px solid #f0f0f0;
            padding: 1.2rem 1.5rem;
            font-weight: 600;
        }

        .stats-card {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
  border-radius: 12px;
  color: #fff;
  padding: 1.5rem;
  text-align: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stats-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.stats-card h3 {
  font-size: 2.4rem;
  font-weight: 700;
  margin: 0;
}

.stats-card p {
  margin: 0.5rem 0 0;
  opacity: 0.9;
  font-weight: 500;
}
    margin: 0;
        }

        .stats-card p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .table {
            background: white;
        }

        .table thead th {
            border-bottom: 2px solid #e0e0e0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #666;
        }

        .badge {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 1rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 0.6rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }

        .profile-dropdown img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }
        .page-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 0.5rem;
}

.breadcrumb {
  background: transparent;
  padding: 0;
  margin-bottom: 1rem;
}

.breadcrumb-item a {
  color: var(--primary-color);
  text-decoration: none;
}

.breadcrumb-item.active {
  color: #666;
  font-weight: 500;
}

.card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

.card-header {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  font-weight: 600;
  border-radius: 12px 12px 0 0;
}
.table thead th {
  background: #f4f6fb;
  font-size: 0.9rem;
  text-transform: uppercase;
  color: #555;
}

.table-hover tbody tr:hover {
  background-color: #f9f9ff;
}

.table img {
  border: 2px solid #e0e0e0;
  border-radius: 50%;
}
.btn-primary.btn-lg {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border: none;
  padding: 0.8rem 2rem;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.btn-primary.btn-lg:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102,126,234,0.3);
}

.btn-group .btn {
  font-size: 0.85rem;
  padding: 0.4rem 0.8rem;
}
.page-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 0.5rem;
}

.breadcrumb {
  background: transparent;
  padding: 0;
  margin-bottom: 1rem;
}

.breadcrumb-item a {
  color: var(--primary-color);
  text-decoration: none;
}

.breadcrumb-item.active {
  color: #666;
  font-weight: 500;
}
.card-header {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  font-weight: 600;
  border-radius: 12px 12px 0 0;
}

.card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.table thead th {
  background: #f4f6fb;
  font-size: 0.9rem;
  text-transform: uppercase;
  color: #555;
}

.table-hover tbody tr:hover {
  background-color: #f9f9ff;
}

.table-bordered td, 
.table-bordered th {
  border-color: #e0e0e0;
}
.badge {
  padding: 0.45rem 0.75rem;
  font-size: 0.8rem;
  border-radius: 6px;
}

.badge.bg-success { background-color: #28a745 !important; }
.badge.bg-danger { background-color: #dc3545 !important; }
.badge.bg-warning { background-color: #ffc107 !important; color: #333; }
.badge.bg-info { background-color: #17a2b8 !important; }
.btn-primary {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border: none;
  border-radius: 8px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102,126,234,0.3);
}

.btn-success {
  background: #28a745;
  border: none;
  border-radius: 8px;
  font-weight: 600;
}
.card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

.card h5 a {
  color: #333;
  font-weight: 600;
}

.card h5 a:hover {
  color: var(--primary-color);
}
.badge {
  padding: 0.45rem 0.75rem;
  font-size: 0.75rem;
  border-radius: 6px;
}

.badge.bg-success { background-color: #28a745 !important; }
.badge.bg-danger { background-color: #dc3545 !important; }
.badge.bg-warning { background-color: #ffc107 !important; color: #333; }
.badge.bg-info    { background-color: #17a2b8 !important; }
.badge.bg-secondary { background-color: #6c757d !important; }
.card-header {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  font-weight: 600;
  border-radius: 12px 12px 0 0;
}

h5.text-primary {
  font-weight: 600;
  font-size: 1.1rem;
  border-left: 4px solid var(--primary-color);
  padding-left: 0.75rem;
}
.form-control, .form-select {
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 0.6rem 1rem;
}

.form-control:focus, .form-select:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
}
input[type="file"] {
  padding: 0.5rem;
  border-radius: 8px;
}
.btn-primary {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border: none;
  border-radius: 8px;
  font-weight: 600;
  padding: 0.6rem 1.5rem;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102,126,234,0.3);
}
h5.text-primary {
  font-weight: 600;
  font-size: 1.1rem;
  border-left: 4px solid var(--primary-color);
  padding-left: 0.75rem;
}
.table thead th {
  background: #f4f6fb;
  font-size: 0.85rem;
  text-transform: uppercase;
  color: #555;
}

.table-hover tbody tr:hover {
  background-color: #f9f9ff;
}
.badge {
  padding: 0.4rem 0.7rem;
  font-size: 0.75rem;
  border-radius: 6px;
}
.badge {
  padding: 0.4rem 0.7rem;
  font-size: 0.75rem;
  border-radius: 6px;
}
.card {
  border-radius: 12px;
}

.card-body {
  background: #fff;
}

.card-body small {
  font-size: 0.75rem;
  letter-spacing: 0.5px;
}

.card-body p {
  color: #333;
}

.badge {
  font-size: 0.8rem;
  border-radius: 6px;
}





    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    @include('partials.sidebar')  

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        @include('partials.navbar')

        <!-- Page Content -->
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Validation Errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>