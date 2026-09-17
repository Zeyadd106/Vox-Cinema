@extends('layouts.app')

@section('title', 'Coming Soon | Vox Cinemas')

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

    /* Reset & Base */
    * {
      font-family: "Poppins", sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      outline: none;
      border: none;
      text-decoration: none;
      text-transform: capitalize;
      transition: 0.3s ease;
    }

    html {
      font-size: 62.5%;
      overflow-x: hidden;
      scroll-behavior: smooth;
      scroll-padding-top: 5rem;
    }

    body {
      background: var(--color-background);
    }

    /* Header */
    .header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background: rgba(26, 26, 26, 0.95);
      padding: 2.5rem 9%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 6px rgba(227, 24, 55, 0.2);
      z-index: 1000;
      border-bottom: 1px solid var(--color-border);
      backdrop-filter: blur(10px);
    }

    .header .logo {
      font-size: 3rem;
      color: var(--color-primary);
      font-weight: bold;
      text-decoration: none;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .navbar {
      display: flex;
      align-items: center;
      gap: 3rem;
    }

    .header .navbar a {
      font-size: 1.8rem;
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
      font-size: 2.5rem;
      cursor: pointer;
      color: var(--color-text);
    }

    /* Coming Soon Section */
    .coming-soon {
      padding: 15rem 9% 5rem;
      background-color: var(--color-background);
      min-height: 100vh;
    }

    .section-title {
      text-align: center;
      font-size: 4rem;
      color: #E31837;
      margin: 1rem 0 4rem;
      text-transform: uppercase;
      animation: fadeInDown 0.5s ease-in-out both;
    }

    /* Movies Grid */
    .box-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 3rem;
      padding: 2rem 0;
    }

    .box {
      background: #1a1a1a;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 5px 20px rgba(227, 24, 55, 0.1);
      border: 1px solid #333;
      transition: transform 0.3s, box-shadow 0.3s;
      animation: fadeInUp 0.8s ease forwards;
      opacity: 0;
    }

    .box:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 30px rgba(227, 24, 55, 0.2);
      border-color: #E31837;
    }

    .box .image {
      position: relative;
      overflow: hidden;
    }

    .box .image img {
      width: 100%;
      height: 400px;
      object-fit: cover;
      transition: transform 0.5s;
    }

    .box:hover .image img {
      transform: scale(1.1);
    }

    .box .content {
      padding: 2rem;
      text-align: center;
    }

    .box .content h3 {
      font-size: 2rem;
      color: #ffffff;
      margin-bottom: 1rem;
    }

    .box .content .release-date {
      font-size: 1.4rem;
      color: #E31837;
      margin-bottom: 1.5rem;
    }

    .box .content .btn {
      display: inline-block;
      padding: 1rem 3rem;
      font-size: 1.6rem;
      color: #ffffff;
      background-color: #E31837;
      border-radius: 30px;
      cursor: pointer;
      border: 2px solid #E31837;
      transition: 0.3s ease;
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 1px;
    }

    .box .content .btn:hover {
      background: transparent;
      color: #E31837;
    }

    @keyframes fadeInUp {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInDown {
      0% {
        opacity: 0;
        transform: translateY(-20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Animation Delays */
    .box:nth-child(1) { animation-delay: 0.1s; }
    .box:nth-child(2) { animation-delay: 0.2s; }
    .box:nth-child(3) { animation-delay: 0.3s; }
    .box:nth-child(4) { animation-delay: 0.4s; }

    /* Responsive Design */
    @media (max-width: 992px) {
      .box-container {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .menu-btn {
        display: block;
      }

      .navbar {
        position: fixed;
        top: 0;
        right: -100%;
        width: 70%;
        height: 100vh;
        background-color: var(--color-surface);
        flex-direction: column;
        justify-content: center;
        padding: var(--spacing-lg);
        transition: var(--transition);
      }

      .navbar.active {
        right: 0;
      }

      .header .navbar a {
        margin: var(--spacing-sm) 0;
        font-size: 2rem;
      }

      .header {
        padding: 2rem 6%;
      }

      .coming-soon {
        padding: 10rem 4% 3rem;
      }
      .section-title {
        font-size: 3rem;
      }
      .box-container {
        gap: 2rem;
      }
      .box .image img {
        height: 300px;
      }
    }

    @media (max-width: 576px) {
      .box-container {
        grid-template-columns: 1fr;
      }
      .section-title {
        font-size: 2.5rem;
      }
    }

    @media (max-width: 480px) {
      .header {
        padding: 1.5rem 4%;
      }

      .header .logo {
        font-size: 2.5rem;
      }

      .header .navbar a {
        font-size: 1.8rem;
      }
    }
</style>
@endsection

@section('content')
  <!-- Coming Soon Section -->
  <section class="coming-soon">
    <h1 class="section-title">Coming Soon</h1>

    <div class="box-container">
      @forelse($comingSoonMovies as $movie)
        <div class="box">
          <div class="image">
            <img src="{{ asset('storage/' . $movie->poster_path) }}" alt="{{ $movie->title }}">
          </div>
          <div class="content">
            <h3>{{ $movie->title }}</h3>
            <p class="release-date">Release Date: {{ $movie->release_date->format('F j, Y') }}</p>
            <button class="btn notify-btn" data-movie-id="{{ $movie->id }}">Notify Me</button>
          </div>
        </div>
      @empty
        <div style="grid-column: 1/-1; text-align: center; color: #cccccc; font-size: 1.8rem; padding: 3rem;">
          No upcoming movies scheduled at the moment.
        </div>
      @endforelse
    </div>
  </section>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Add animation class to boxes on scroll
    const boxes = document.querySelectorAll('.box');
    
    function checkBoxes() {
      const triggerBottom = window.innerHeight * 0.8;
      
      boxes.forEach(box => {
        const boxTop = box.getBoundingClientRect().top;
        
        if(boxTop < triggerBottom) {
          box.style.opacity = '1';
        }
      });
    }

    window.addEventListener('scroll', checkBoxes);
    checkBoxes(); // Check on initial load

    // Handle notify button clicks
    document.querySelectorAll('.notify-btn').forEach(button => {
      button.addEventListener('click', async () => {
        const movieId = button.dataset.movieId;
        try {
          const response = await fetch('/api/notify-me', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ movie_id: movieId })
          });

          if (response.ok) {
            button.textContent = 'Notification Set';
            button.disabled = true;
            button.style.backgroundColor = '#1a1a1a';
            button.style.borderColor = '#1a1a1a';
          } else {
            throw new Error('Failed to set notification');
          }
        } catch (error) {
          console.error('Error:', error);
          alert('Failed to set notification. Please try again later.');
        }
      });
    });
  });
</script>
@endsection 