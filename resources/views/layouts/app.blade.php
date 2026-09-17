<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | Vox Cinemas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --color-primary: #E31837;
            --color-primary-dark: #c41530;
            --color-primary-light: #ff1f3d;
        }

        /* Global styles that won't be reset */
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-color: #000000;
            color: #ffffff;
        }

        /* Header Logo Styles - More specific selectors */
        body > .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 1rem 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            box-shadow: 0 2px 6px rgba(227, 24, 55, 0.2);
            font-family: 'Poppins', sans-serif;
        }

        body > .header .logo-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            z-index: 1001;
        }

        body > .header .small-logo {
            width: 30px;
            height: 30px;
            transition: transform 0.3s ease;
            display: block;
        }

        body > .header .logo-wrapper:hover .small-logo {
            transform: scale(1.1) rotate(10deg);
        }

        body > .header .logo-text {
            font-size: 2rem;
            color: var(--color-primary);
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            font-family: 'Poppins', sans-serif;
        }

        body > .header .logo-text::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--color-primary);
            transition: width 0.3s ease;
        }

        body > .header .logo-wrapper:hover .logo-text::after {
            width: 100%;
        }

        body > .header .navbar {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        body > .header .navbar a {
            color: #fff;
            text-decoration: none;
            font-size: 1.1rem;
            transition: color 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        body > .header .navbar a:hover {
            color: var(--color-primary);
        }

        @media (max-width: 768px) {
            body > .header .logo-text {
                font-size: 1.5rem;
            }
            body > .header .small-logo {
                width: 25px;
                height: 25px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <header class="header">
        <a href="{{ route('home') }}" class="logo-wrapper">
            <img src="{{ asset('images/vox-logo.png') }}" alt="Vox Cinemas" class="small-logo">
            <span class="logo-text">Vox Cinemas</span>
        </a>
        <div class="menu-btn">☰</div>
        <nav class="navbar">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('coming-soon') }}">Coming Soon</a>
            <a href="{{ route('movies.index') }}">Dashboard</a>
            @guest
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @else
                <a href="{{ route('bookings.index') }}">My Bookings</a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endguest
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="credit">
            <span>&copy; 2025 Vox Cinemas. All rights reserved.</span>
        </div>
    </footer>

    @yield('scripts')
    <script>
        // Mobile menu functionality
        const menuBtn = document.querySelector('.menu-btn');
        const navbar = document.querySelector('.navbar');

        menuBtn.addEventListener('click', () => {
            navbar.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!navbar.contains(e.target) && !menuBtn.contains(e.target)) {
                navbar.classList.remove('active');
            }
        });
    </script>
</body>
</html>
