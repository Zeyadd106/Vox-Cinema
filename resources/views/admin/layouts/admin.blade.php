<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') | Vox Cinemas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #000000;
            color: #ffffff;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #1a1a1a;
            color: #ffffff;
            padding: 30px;
            border-right: 1px solid #333;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .sidebar-logo img {
            width: 40px;
            height: 40px;
            transition: transform 0.3s ease;
        }

        .sidebar-logo:hover img {
            transform: scale(1.1) rotate(10deg);
        }

        .sidebar-logo span {
            color: #E31837;
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .sidebar h3 {
            color: #E31837;
            font-size: 20px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 20px 0;
        }

        .sidebar ul li a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            display: block;
            padding: 12px 20px;
            border-radius: 30px;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .sidebar ul li a.active,
        .sidebar ul li a:hover {
            color: #E31837;
            background: #2a2a2a;
            border-color: #E31837;
            transform: translateX(5px);
        }

        /* Main Content */
        .main-content {
            margin-left: 270px;
            padding: 40px;
            max-width: 1200px;
        }

        .main-content h1 {
            text-align: center;
            color: #E31837;
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 40px;
            text-transform: uppercase;
        }

        .section {
            display: none;
            animation: fadeIn 0.3s ease-out;
        }

        .section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 15px;
            border: 1px solid #333;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: #E31837;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(227, 24, 55, 0.2);
        }

        .stat-card h3 {
            color: #E31837;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 24px;
            font-weight: 600;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-top: 30px;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            background: #1a1a1a;
            border: 1px solid #333;
        }

        table th {
            background: #2a2a2a;
            color: #E31837;
            font-weight: 600;
            text-transform: uppercase;
        }

        table tr {
            transition: all 0.3s ease;
        }

        table tr:hover td {
            border-color: #E31837;
            background: #2a2a2a;
        }

        /* Forms */
        .form-container {
            background: #1a1a1a;
            padding: 30px;
            border-radius: 15px;
            margin-top: 30px;
            border: 1px solid #333;
            transition: all 0.3s ease;
        }

        .form-container:hover {
            border-color: #E31837;
            box-shadow: 0 10px 30px rgba(227, 24, 55, 0.2);
        }

        .form-container h2 {
            color: #E31837;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #E31837;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 20px;
            background: #2a2a2a;
            border: 1px solid #333;
            border-radius: 30px;
            color: #ffffff;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group textarea {
            border-radius: 15px;
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #E31837;
            box-shadow: 0 0 10px rgba(227, 24, 55, 0.1);
        }

        .form-group input[type="file"] {
            background: transparent;
            padding: 10px;
            border: 1px dashed #333;
            cursor: pointer;
        }

        .form-group input[type="file"]:hover {
            border-color: #E31837;
        }

        /* Buttons */
        .btn {
            padding: 12px 25px;
            background-color: #E31837;
            color: #ffffff;
            border: 2px solid #E31837;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            margin: 5px;
        }

        .btn:hover {
            background-color: transparent;
            color: #E31837;
            transform: translateY(-2px);
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
                padding: 20px;
            }

            .main-content {
                margin-left: 220px;
                padding: 20px;
            }

            .main-content h1 {
                font-size: 24px;
            }

            .stat-card {
                padding: 15px;
            }

            .stat-card h3 {
                font-size: 16px;
            }

            .stat-card p {
                font-size: 20px;
            }

            table th, table td {
                padding: 10px;
                font-size: 14px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 12px;
            }
        }
    </style>
    @yield('additional_styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
                <img src="{{ asset('images/vox-logo.png') }}" alt="Vox Cinemas">
                <span>Admin</span>
            </a>
            <h3>Dashboard</h3>
        </div>
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('admin.movies.index') }}" class="{{ request()->routeIs('admin.movies.*') ? 'active' : '' }}">Movies</a></li>
            <li><a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">Bookings</a></li>
            <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a></li>
            <li><a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}">Settings</a></li>
            <li>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    @yield('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Navigation
            const navLinks = document.querySelectorAll('.nav-link');
            const sections = document.querySelectorAll('.section');

            function showSection(sectionId) {
                sections.forEach(section => {
                    section.classList.remove('active');
                });
                document.getElementById(sectionId)?.classList.add('active');

                navLinks.forEach(link => {
                    link.classList.remove('active');
                });
                document.querySelector(`[data-section="${sectionId}"]`)?.classList.add('active');
            }

            navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    const sectionId = link.getAttribute('data-section');
                    if (sectionId) {
                        e.preventDefault();
                        showSection(sectionId);
                    }
                });
            });

            // Form Display Functions
            window.closeForm = function(formId) {
                document.getElementById(formId).style.display = 'none';
            };
        });
    </script>
</body>
</html> 