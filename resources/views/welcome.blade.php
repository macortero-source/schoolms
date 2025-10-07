<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'School Management System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        .hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;              /* full screen height */
    display: flex;
    flex-direction: column;         /* stack content + icon */
    justify-content: center;        /* vertical center */
    align-items: center;            /* horizontal center */
    text-align: center;             /* center text */
    color: white;
    position: relative;             /* for background icon positioning */
    overflow: hidden;
}

        .hero-content {
    max-width: 700px;
    z-index: 2;                     /* keep text above icon */
}

.hero-section .background-icon {
    position: absolute;
    bottom: 5%;                     /* push icon lower */
    left: 50%;
    transform: translateX(-50%);
    font-size: 20rem;
    opacity: 0.1;                   /* faint watermark effect */
    z-index: 1;
    pointer-events: none;           /* icon won’t block clicks */
}

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-image {
            max-width: 100%;
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 2rem;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .btn-custom {
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-light-custom {
            background: white;
            color: #667eea;
            font-weight: 600;
            border-radius: 50px;
            padding: 0.8rem 2rem;
            transition: all 0.3s;
        }

        .btn-light-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255,255,255,0.3);
        }

        .btn-outline-custom {
            border: 2px solid white;
            color: white;
            font-weight: 600;
            border-radius: 50px;
            padding: 0.8rem 2rem;
            transition: all 0.3s;
        }

        .btn-outline-custom:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
        }

        .features-section {
            padding: 5rem 0;
            background: #f8f9fa;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
        }

        .stats-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item h3 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-item p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        footer {
            background: #333;
            color: white;
            padding: 2rem 0;
            text-align: center;
        }
        nav {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000; /* mas mataas kaysa hero */
   background: transparent;
  transition: background 0.3s ease, backdrop-filter 0.3s ease;
}

.navbar-scroll {
  background: rgba(102,126,234,0.9); /* same gradient tone */
  backdrop-filter: blur(6px);
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="absolute top-0 left-0 w-full flex items-center justify-between px-8 py-4 text-white z-50">
  <a href="#" class="flex items-center space-x-2">
    <i class="fas fa-graduation-cap"></i>
    <span class="font-bold">School MS</span>
  </a>

  <!-- Auth Links -->
  <div class="flex items-center space-x-4">
    <a href="{{ route('login') }}" class="px-4 py-2 border border-white rounded-full hover:bg-white hover:text-indigo-600 transition">Login</a>
    <a href="{{ route('register') }}" class="px-4 py-2 bg-white text-indigo-600 font-semibold rounded-full hover:bg-gray-200 transition">Register</a>
  </div>
</nav>

    <!-- Hero Section -->
    <section class="hero-section">
    <div class="hero-content">
        <h1>Modern School Management System</h1>
        <p>Streamline your school operations with our comprehensive, easy-to-use management platform.</p>
        <div>
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-light-custom btn-lg me-3">
                    Go to Dashboard <i class="fas fa-arrow-right ms-2"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-light-custom btn-lg me-3">
                    Get Started <i class="fas fa-arrow-right ms-2"></i>
                </a>
                <a href="#features" class="btn btn-outline-custom btn-lg">
                    Learn More
                </a>
            @endauth
        </div>
    </div>
    <!-- Background icon -->
    <i class="fas fa-school background-icon"></i>
</section>


    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Powerful Features</h2>
                <p class="text-muted">Everything you need to manage your school efficiently</p>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h4>Student Management</h4>
                        <p class="text-muted">Comprehensive student profiles, enrollment, and academic tracking</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h4>Teacher Portal</h4>
                        <p class="text-muted">Manage classes, subjects, attendance, and grade entry efficiently</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h4>Attendance Tracking</h4>
                        <p class="text-muted">Real-time attendance marking with detailed reports and analytics</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4>Exam Management</h4>
                        <p class="text-muted">Create exams, enter grades, and generate comprehensive reports</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Advanced Analytics</h4>
                        <p class="text-muted">Detailed performance metrics and data-driven insights</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h4>Announcements</h4>
                        <p class="text-muted">Broadcast important information to students, teachers, and parents</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <h3>500+</h3>
                        <p>Students</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <h3>50+</h3>
                        <p>Teachers</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <h3>20+</h3>
                        <p>Classes</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <h3>99%</h3>
                        <p>Satisfaction</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} School Management System. All rights reserved.</p>
            <p class="mb-0 mt-2">
                <small>Developed for Academic Project</small>
            </p>
        </div>
    </footer>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    const nav = document.querySelector("nav");

    window.addEventListener("scroll", function() {
        if (window.scrollY > 50) {
            nav.classList.add("navbar-scroll");
        } else {
            nav.classList.remove("navbar-scroll");
        }
    });
});
</script>

</body>
</html>