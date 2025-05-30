<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        body {
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            width: 240px;
            background-color: #343a40;
            padding-top: 1rem;
            color: #fff;
        }
        .sidebar a {
            color: #adb5bd;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
            color: #fff;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .sidebar .nav-header {
            font-size: 1.1rem;
            padding: 10px 20px;
            font-weight: bold;
            color: #ced4da;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <div class="text-center mb-4">
            <h4 class="text-white">Admin Panel</h4>
        </div>

        <div class="nav-header">Navigation</div>

        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home me-2"></i> Dashboard
        </a>

        <a href="{{ route('admin.academic_year.create') }}" class="{{ request()->routeIs('admin.academic_year.create') ? 'active' : '' }}">
            <i class="fas fa-calendar-plus me-2"></i> Create Academic Year
        </a>
<a href="{{ route('admin.course.create') }}" class="{{ request()->routeIs('admin.course.create') ? 'active' : '' }}">
    <i class="fas fa-chalkboard me-2"></i> Create Course
</a>




        <div class="mt-auto p-3">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-light w-100">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

</body>
</html>
