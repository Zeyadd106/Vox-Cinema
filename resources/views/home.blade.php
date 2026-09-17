@extends('layouts.app')

@section('title', 'Home')

@section('styles')
<style>
    :root {
      --color-primary: #E31837;
      --color-primary-dark: #c41530;
      --color-primary-light: #ff1f3d;
      --color-background: #000000;
      --color-surface: #1a1a1a;
      --color-surface-light: #2a2a2a;
      --color-text: #ffffff;
      --color-text-secondary: #cccccc;
      --color-border: #333333;

      --spacing-xs: 0.5rem;
      --spacing-sm: 1rem;
      --spacing-md: 2rem;
      --spacing-lg: 3rem;

      --border-radius: 8px;
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: var(--color-background);
      color: var(--color-text);
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Preloader styles */
    .preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: var(--color-background);
        z-index: 10000;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Particle effect container */
    .particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 9998;
    }

    .particle {
        position: absolute;
        background: var(--color-primary);
        border-radius: 50%;
        pointer-events: none;
        opacity: 0;
        box-shadow: 0 0 10px var(--color-primary);
    }

    /* Loading Screen */
    .loading-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: var(--color-background);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        perspective: 1500px;
        opacity: 0;
        animation: fadeIn 0.3s ease-out forwards;
    }

    .loading-container {
        position: relative;
        width: 200px;
        height: 200px;
        transform-style: preserve-3d;
        animation: container-spin 2s ease-out forwards;
        animation-play-state: paused;
    }

    .loading-logo {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        filter: drop-shadow(0 0 20px var(--color-primary));
        animation: logo-sequence 2s ease-out forwards;
        animation-play-state: paused;
    }

    .loading-ring {
        position: absolute;
        width: 300px;
        height: 300px;
        border: 2px solid var(--color-primary);
        border-radius: 50%;
        transform-style: preserve-3d;
        animation: ring-animation 2s ease-out infinite;
        opacity: 0;
        box-shadow: 0 0 20px var(--color-primary);
    }

    .animate .loading-container,
    .animate .loading-logo,
    .animate .loading-ring {
        animation-play-state: running;
    }

    @keyframes container-spin {
        0% {
            transform: rotateY(0) scale(0.3) translateZ(-100px);
            filter: brightness(0);
        }
        30% {
            transform: rotateY(360deg) scale(1.2) translateZ(100px);
            filter: brightness(1.5) drop-shadow(0 0 20px var(--color-primary));
        }
        50% {
            transform: rotateY(360deg) scale(1) translateZ(0);
            filter: brightness(1) drop-shadow(0 0 30px var(--color-primary));
        }
        80% {
            transform: rotateY(360deg) scale(1) translateZ(0) rotateX(0);
            opacity: 1;
            filter: drop-shadow(0 0 40px var(--color-primary));
        }
        100% {
            transform: scale(0) rotate(360deg);
            opacity: 0;
            filter: drop-shadow(0 0 50px var(--color-primary));
        }
    }

    @keyframes ring-animation {
        0% {
            transform: rotateX(70deg) rotateY(0deg) scale(0.5);
            opacity: 0;
            border-width: 2px;
            border-color: var(--color-primary-dark);
        }
        20% {
            opacity: 0.5;
            border-width: 5px;
            border-color: var(--color-primary);
        }
        50% {
            transform: rotateX(70deg) rotateY(180deg) scale(1.2);
            opacity: 0.8;
            border-width: 2px;
            border-color: var(--color-primary-light);
        }
        100% {
            transform: rotateX(70deg) rotateY(360deg) scale(0.5);
            opacity: 0;
            border-width: 5px;
            border-color: var(--color-primary);
        }
    }

    @keyframes logo-sequence {
        0% {
            transform: rotate(0deg) scale(1);
            filter: brightness(0) drop-shadow(0 0 0 var(--color-primary));
        }
        30% {
            transform: rotate(-180deg) scale(1.2);
            filter: brightness(1.5) drop-shadow(0 0 30px var(--color-primary));
        }
        50% {
            transform: rotate(-180deg) scale(1);
            filter: brightness(1) drop-shadow(0 0 20px var(--color-primary));
        }
        80% {
            transform: rotate(-360deg) scale(1.1);
            filter: brightness(1.2) drop-shadow(0 0 40px var(--color-primary));
            opacity: 1;
        }
        100% {
            transform: rotate(-720deg) scale(0);
            filter: brightness(2) drop-shadow(0 0 50px var(--color-primary));
            opacity: 0;
        }
    }

    /* Glitch effect for text */
    .logo-text::before,
    .logo-text::after {
        content: attr(data-text);
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        opacity: 0.8;
        filter: blur(1px);
        animation: glitch 5s infinite alternate-reverse;
    }

    .logo-text::before {
        left: 2px;
        text-shadow: -2px 0 var(--color-primary);
        animation-delay: -1s;
    }

    .logo-text::after {
        left: -2px;
        text-shadow: 2px 0 var(--color-primary-dark);
        animation-delay: -2s;
    }

    @keyframes glitch {
        0% { transform: translateX(0); }
        20% { transform: translateX(-2px); }
        40% { transform: translateX(2px); }
        60% { transform: translateX(-1px); }
        80% { transform: translateX(1px); }
        100% { transform: translateX(0); }
    }

    /* Header */
    .header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background: rgba(26, 26, 26, 0.95);
      padding: var(--spacing-sm) 6%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 6px rgba(227, 24, 55, 0.2);
      z-index: 1000;
      border-bottom: 1px solid var(--color-border);
      backdrop-filter: blur(10px);
    }

    .header .logo-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .header .small-logo {
        width: 30px;
        height: 30px;
        opacity: 0;
        transform: scale(0);
        animation: small-logo-appear 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        animation-delay: 2.2s;
    }

    .header .logo-text {
        font-size: 2rem;
        color: var(--color-primary);
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        opacity: 1;
        position: relative;
    }

    @keyframes small-logo-appear {
        0% {
            opacity: 0;
            transform: scale(0) rotate(180deg);
        }
        100% {
            opacity: 1;
            transform: scale(1) rotate(0);
        }
    }

    .logo-image {
      height: 40px;
      width: auto;
      transform-origin: center;
      animation: logoEntrance 1.5s ease-out, logoFloat 3s ease-in-out infinite;
      transition: transform 0.3s ease;
    }

    .logo:hover .logo-image {
      transform: scale(1.1) rotate(5deg);
      filter: drop-shadow(0 0 10px var(--color-primary));
    }

    @keyframes logoEntrance {
      0% {
        opacity: 0;
        transform: translateY(-20px) scale(0.8);
      }
      50% {
        transform: translateY(5px) scale(1.1);
      }
      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes logoFloat {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-5px);
      }
    }

    @keyframes logoPulse {
      0%, 100% {
        filter: drop-shadow(0 0 5px var(--color-primary));
      }
      50% {
        filter: drop-shadow(0 0 15px var(--color-primary-light));
      }
    }

    .logo-image {
      animation: 
        logoEntrance 1.5s ease-out,
        logoFloat 3s ease-in-out infinite,
        logoPulse 2s ease-in-out infinite;
    }

    .navbar {
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
    }

    .header .navbar a {
      font-size: 1.1rem;
      color: var(--color-text);
      text-decoration: none;
      transition: var(--transition);
      position: relative;
      font-weight: 500;
    }

    .header .navbar a::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background-color: var(--color-primary);
      transition: var(--transition);
    }

    .header .navbar a:hover::after {
      width: 100%;
    }

    .menu-btn {
      display: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--color-text);
    }

    /* Hero Section */
    .hero {
      height: 100vh;
      background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)),
                  url('{{ asset('images/cinema-bg.jpg') }}') center/cover no-repeat;
      background-size: cover;
      background-position: center;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 0 var(--spacing-md);
      position: relative;
      overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 1;
    }

    .hero__content {
      max-width: 900px;
      animation: fadeIn 1s ease;
      position: relative;
      z-index: 2;
      color: var(--color-text);
    }

    .hero__subtitle {
      font-size: 1.8rem;
      color: var(--color-primary-light);
      margin-bottom: var(--spacing-sm);
      text-transform: uppercase;
      letter-spacing: 4px;
      font-weight: 600;
      text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
    }

    .hero__title {
      font-size: 5rem;
      color: var(--color-text);
      margin-bottom: var(--spacing-lg);
      line-height: 1.1;
      text-transform: uppercase;
      letter-spacing: 2px;
      font-weight: 700;
      text-shadow: 2px 2px 6px rgba(0,0,0,0.5);
    }

    /* General Section Styling */
    .section {
        padding: var(--spacing-lg) 6%;
        background-color: var(--color-background);
    }

    .section__title {
        font-size: 2.8rem;
        color: var(--color-primary);
        text-align: center;
        margin-bottom: var(--spacing-lg);
        text-transform: uppercase;
        letter-spacing: 2px;
        position: relative;
        padding-bottom: var(--spacing-sm);
    }

    .section__title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background-color: var(--color-primary-light);
        border-radius: 2px;
    }

    /* Movies Grid */
    .movies__grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: var(--spacing-lg);
      max-width: 1500px;
      margin: 0 auto;
      padding-top: var(--spacing-md);
    }

    .movie-card {
      background-color: var(--color-surface);
      border-radius: var(--border-radius);
      overflow: hidden;
      transition: transform 0.4s ease, box-shadow 0.4s ease;
      animation: fadeInUp 0.6s ease;
      border: 1px solid var(--color-border);
      display: flex;
      flex-direction: column;
      cursor: pointer;
    }

    .movie-card:hover {
      transform: translateY(-15px);
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
      border-color: var(--color-primary);
    }

    .movie-card__image-wrapper {
      position: relative;
      padding-top: 140%; /* Aspect ratio for movie posters */
      overflow: hidden;
    }

    .movie-card__image {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .movie-card:hover .movie-card__image {
      transform: scale(1.05);
    }

    .movie-card__content {
      padding: var(--spacing-md);
      text-align: center;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .movie-card__title {
      font-size: 1.4rem;
      color: var(--color-text);
      margin-bottom: var(--spacing-sm);
      font-weight: 600;
    }

     .movie-card__title a {
        color: inherit;
        text-decoration: none;
     }

     .movie-card__title a:hover {
        color: var(--color-primary-light);
     }

    .movie-card__btn {
      display: inline-block;
      background-color: var(--color-primary);
      color: var(--color-text);
      border: none;
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--border-radius);
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      border: 2px solid var(--color-primary);
      text-decoration: none;
      margin-top: var(--spacing-sm);
    }

    .movie-card__btn:hover {
      background-color: transparent;
      color: var(--color-primary-light);
      border-color: var(--color-primary-light);
    }

    /* Notification */
    .notification {
      position: relative;
      margin-left: var(--spacing-md);
    }

    .notification__icon {
      color: var(--color-text);
      font-size: 1.2rem;
      cursor: pointer;
      position: relative;
    }

    .notification__badge {
      position: absolute;
      top: -8px;
      right: -8px;
      background-color: var(--color-primary);
      color: var(--color-text);
      font-size: 0.8rem;
      padding: 2px 6px;
      border-radius: 50%;
      font-weight: 600;
    }

    .notification__dropdown {
      position: absolute;
      top: 100%;
      right: 0;
      width: 300px;
      background-color: var(--color-surface);
      border-radius: var(--border-radius);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      border: 1px solid var(--color-border);
      display: none;
      z-index: 1000;
    }

    .notification__dropdown.show {
      display: block;
      animation: fadeInDown 0.3s ease;
    }

    .notification__header {
      padding: var(--spacing-sm);
      border-bottom: 1px solid var(--color-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .notification__title {
      font-weight: 600;
      color: var(--color-primary);
    }

    .notification__close {
      background: none;
      border: none;
      color: var(--color-text-secondary);
      cursor: pointer;
      font-size: 1.2rem;
    }

    .notification__list {
      max-height: 300px;
      overflow-y: auto;
    }

    .notification__item {
      padding: var(--spacing-sm);
      border-bottom: 1px solid var(--color-border);
      transition: var(--transition);
    }

    .notification__item:hover {
      background-color: var(--color-surface-light);
    }

    .notification__item-title {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--color-text);
      margin-bottom: 4px;
    }

    .notification__item-desc {
      font-size: 0.8rem;
      color: var(--color-text-secondary);
      margin-bottom: 4px;
    }

    .notification__item-time {
      font-size: 0.7rem;
      color: var(--color-text-secondary);
    }

    /* Animations */
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero__title {
            font-size: 4rem;
        }
        .section__title {
            font-size: 2.4rem;
        }
        .movies__grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: var(--spacing-md);
        }
         .movie-card__title {
            font-size: 1.3rem;
        }
    }

    @media (max-width: 768px) {
        .hero__title {
            font-size: 3rem;
        }
        .hero__subtitle {
            font-size: 1.5rem;
        }
        .section {
            padding: var(--spacing-md) 4%;
        }
        .section__title {
            font-size: 2rem;
        }
        .movies__grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: var(--spacing-md);
        }
        .movie-card__title {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 480px) {
        .hero__title {
            font-size: 2.5rem;
        }
        .hero__subtitle {
            font-size: 1.2rem;
        }
        .section__title {
            font-size: 1.8rem;
        }
        .movies__grid {
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: var(--spacing-sm);
        }
        .movie-card__title {
            font-size: 1.1rem;
        }
        .movie-card__btn {
            padding: var(--spacing-sm) var(--spacing-sm);
            font-size: 0.9rem;
        }
    }

    /* Accessibility */
    @media (prefers-reduced-motion: reduce) {
      * {
        animation: none !important;
        transition: none !important;
      }
    }

    @media (prefers-contrast: high) {
      :root {
        --color-primary: #ff0000;
      }

      .movie-card {
        border: 2px solid var(--color-text);
      }

      .notification__badge {
        border: 2px solid var(--color-text);
      }
    }

    /* New Footer Styles */
    .site-footer {
        background-color: var(--color-surface-light);
        padding: var(--spacing-sm) 6%;
        color: var(--color-text-secondary);
        font-size: 0.9rem;
        border-top: 1px solid var(--color-border);
    }

    .site-footer .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-content {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-md);
    }

    .footer-section {
        flex: 1;
        min-width: 200px;
        margin-bottom: var(--spacing-md);
    }

    .footer-section h2 {
        color: var(--color-primary);
        font-size: 1.2rem;
        margin-bottom: var(--spacing-sm);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .footer-section p {
        margin-bottom: var(--spacing-sm);
        line-height: 1.8;
    }

    .footer-section ul {
        list-style: none;
    }

    .footer-section ul li {
        margin-bottom: 8px;
    }

    .footer-section ul li a {
        color: var(--color-text-secondary);
        text-decoration: none;
        transition: var(--transition);
    }

    .footer-section ul li a:hover {
        color: var(--color-primary-light);
        text-decoration: underline;
    }

    .footer-section .fas {
        margin-right: 8px;
        color: var(--color-primary);
    }

    .footer-bottom {
        text-align: center;
        margin-top: var(--spacing-md);
        padding-top: var(--spacing-md);
        border-top: 1px solid var(--color-border);
        color: var(--color-text-secondary);
        font-size: 0.8rem;
    }

     /* Search bar styles */
    .search-container {
        display: flex;
        justify-content: center;
        margin-bottom: var(--spacing-lg);
        position: relative;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .search-input {
        width: 100%;
        padding: var(--spacing-sm) var(--spacing-md);
        padding-right: 40px; /* Space for icon */
        border-radius: var(--border-radius);
        border: 1px solid var(--color-border);
        background-color: var(--color-surface);
        color: var(--color-text);
        font-size: 1rem;
        transition: var(--transition);
    }

    .search-input::placeholder {
        color: var(--color-text-secondary);
        opacity: 0.8;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--color-primary-light);
        box-shadow: 0 0 8px rgba(var(--color-primary-light), 0.3);
    }

    .search-icon {
        position: absolute;
        top: 50%;
        right: var(--spacing-sm);
        transform: translateY(-50%);
        color: var(--color-text-secondary);
        font-size: 1.2rem;
        pointer-events: none; /* Allow clicking on input */
    }

    /* Responsive adjustments for footer */
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            text-align: center;
        }
        .footer-section {
            min-width: auto;
        }
        .footer-section ul {
            padding: 0;
        }
    }

    @keyframes particle-fade {
        0% { 
            opacity: 0;
            filter: drop-shadow(0 0 5px var(--color-primary));
        }
        20% { 
            opacity: 0.8;
            filter: drop-shadow(0 0 10px var(--color-primary));
        }
        80% { 
            opacity: 0.8;
            filter: drop-shadow(0 0 15px var(--color-primary));
        }
        100% { 
            opacity: 0;
            filter: drop-shadow(0 0 5px var(--color-primary));
        }
    }

    @keyframes particle-move {
        0% {
            transform: translate(0, 0) scale(1);
        }
        100% {
            transform: translate(
                ${Math.random() * 200 - 100}px,
                ${Math.random() * 200 - 100}px
            ) scale(0);
        }
    }
</style>
@endsection

@section('content')
    <!-- Preloader -->
    <div class="preloader">
        <div class="loading-dots"></div>
    </div>

    <!-- Particles Container -->
    <div class="particles"></div>

    <!-- Loading Screen -->
    <div class="loading-screen">
        <div class="loading-ring"></div>
        <div class="loading-container">
            <img src="{{ asset('images/vox-logo.png') }}" alt="Vox Cinemas" class="loading-logo">
        </div>
    </div>

    <header class="header">
        <a href="{{ route('home') }}" class="logo-wrapper">
            <img src="{{ asset('images/vox-logo.png') }}" alt="Vox Cinemas" class="small-logo">
            <span class="logo-text" data-text="Vox Cinemas">Vox Cinemas</span>
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
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" style="background: none; border: none; cursor: pointer; color: inherit; font: inherit;">Logout</button>
                </form>
            @endguest
        </nav>
    </header>

    <section class="hero">
      <div class="hero__content">
        <p class="hero__subtitle">Experience the Magic of Cinema</p>
        <h1 class="hero__title">Your Ultimate Movie Destination</h1>
      </div>
    </section>

    <section class="section movies">
      <h2 class="section__title">Now Showing</h2>
      <div class="search-container">
          <input type="text" id="movie-search" class="search-input" placeholder="Search for movies...">
          <i class="fas fa-search search-icon"></i>
      </div>
      <div class="movies__grid">
        @foreach($currentMovies as $movie)
        <article class="movie-card" data-title="{{ strtolower($movie->title) }}">
          <div class="movie-card__image-wrapper">
            <img src="{{ asset('storage/' . $movie->poster_path) }}" alt="{{ $movie->title }}" class="movie-card__image">
          </div>
          <div class="movie-card__content">
            <h3 class="movie-card__title"><a href="{{ route('movies.show', $movie->id) }}">{{ $movie->title }}</a></h3>
            <a href="{{ route('bookings.book-movie', $movie->id) }}" class="movie-card__btn">Book Now</a>
          </div>
        </article>
        @endforeach
        {{-- Hardcoded example movie card --}}
        <article class="movie-card" data-title="hardcoded movie">
          <div class="movie-card__image-wrapper">
            <img src="{{ asset('images/placeholder.jpg') }}" alt="Hardcoded Movie" class="movie-card__image">
          </div>
          <div class="movie-card__content">
            <h3 class="movie-card__title"><a href="#">Hardcoded Movie Title</a></h3>
            <a href="#" class="movie-card__btn">Book Now</a>
          </div>
        </article>
      </div>
    </section>

    <footer class="site-footer">
      <div class="container">
          <div class="footer-content">
              <div class="footer-section about">
                  <h2>About Vox Cinemas</h2>
                  <p>Bringing you the latest blockbusters and timeless classics. Experience the best in entertainment.</p>
              </div>
              <div class="footer-section links">
                  <h2>Quick Links</h2>
                  <ul>
                      <li><a href="{{ route('home') }}">Home</a></li>
                      <li><a href="{{ route('coming-soon') }}">Coming Soon</a></li>
                      <li><a href="{{ route('movies.index') }}">Movies</a></li>
                      @auth
                          <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
                      @endauth
                  </ul>
              </div>
              <div class="footer-section contact">
                  <h2>Contact Us</h2>
                  <p><i class="fas fa-envelope"></i> info@voxcinemas.com</p>
                  <p><i class="fas fa-phone"></i> +123 456 7890</p>
              </div>
          </div>
      </div>
      <div class="footer-bottom">
          &copy; 2025 Vox Cinemas. All rights reserved.
      </div>
    </footer>

@endsection

@section('scripts')
<script>
    // Function to start animation sequence
    function startAnimation() {
        const loadingScreen = document.querySelector('.loading-screen');
        const preloader = document.querySelector('.preloader');
        
        if (loadingScreen && preloader) {
            preloader.style.display = 'none';
            loadingScreen.classList.add('animate');
            createParticles();
            
            setTimeout(() => {
                loadingScreen.style.opacity = '0';
                setTimeout(() => {
                    loadingScreen.remove();
                }, 300);
            }, 2000); // Reduced from 5000 to 2000
        }
    }

    // Create particle effect
    function createParticles() {
        const particlesContainer = document.querySelector('.particles');
        const particleCount = 30; // Reduced from 50 for better performance
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            
            // Random size between 2 and 4 pixels
            const size = Math.random() * 2 + 2;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            
            // Initial position
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.top = `${Math.random() * 100}%`;
            
            particlesContainer.appendChild(particle);
            animateParticle(particle);
        }
    }

    function animateParticle(particle) {
        const duration = Math.random() * 1 + 1; // Faster duration
        const delay = Math.random() * 2; // Shorter delay
        
        particle.style.animation = `
            particle-fade ${duration}s ease-in ${delay}s infinite,
            particle-move ${duration}s ease-in ${delay}s infinite
        `;
    }

    // Cache images before starting animation
    function preloadImages() {
        return new Promise((resolve) => {
            const logoImg = new Image();
            logoImg.src = "{{ asset('images/vox-logo.png') }}";
            logoImg.onload = resolve;
        });
    }

    // Initialize animation sequence
    async function initAnimation() {
        await preloadImages();
        startAnimation();
    }

    // Start animation when page loads
    window.addEventListener('load', initAnimation);

    // Restart animation on navigation
    document.addEventListener('DOMContentLoaded', () => {
        const links = document.querySelectorAll('a:not([target="_blank"])');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                if (!e.ctrlKey && !e.shiftKey && !e.metaKey) {
                    e.preventDefault();
                    const href = link.getAttribute('href');
                    
                    // Start animation
                    const loadingScreen = document.querySelector('.loading-screen');
                    if (loadingScreen) {
                        loadingScreen.style.opacity = '1';
                        loadingScreen.classList.add('animate');
                        
                        setTimeout(() => {
                            window.location.href = href;
                        }, 1000); // Shorter delay for navigation
                    } else {
                        window.location.href = href;
                    }
                }
            });
        });
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', () => {
        initAnimation();
    });

    // Rest of your existing scripts
    // Add animation delay to movie cards
    document.querySelectorAll('.movie-card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Intersection Observer for scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.2, rootMargin: '0px 0px -50px 0px' });

    // Observe movie cards
    document.querySelectorAll('.movie-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        observer.observe(card);
    });

    // Movie search functionality
    const searchInput = document.getElementById('movie-search');
    const movieCards = document.querySelectorAll('.movies__grid .movie-card');

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            movieCards.forEach(card => {
                const movieTitle = card.getAttribute('data-title');
                if (movieTitle && movieTitle.includes(searchTerm)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
</script>
@endsection

© 2025 Vox Cinemas. All rights reserved.
