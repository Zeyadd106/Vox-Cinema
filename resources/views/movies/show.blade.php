@extends('layouts.app')

@section('title', $movie->title)

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
      --color-rating: #FFC107;

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
    }

    /* Movie Detail Styles */
    .movie-detail {
      padding-top: 120px;
      padding-bottom: var(--spacing-lg);
    }

    .movie-hero {
      position: relative;
      height: 500px;
      background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.9)),
                  url('{{ asset('storage/' . $movie->poster_path) }}') center/cover;
      margin-bottom: var(--spacing-lg);
    }

    .movie-info-container {
      width: 85%;
      max-width: 1400px;
      margin: 0 auto;
      padding: var(--spacing-md);
      background-color: var(--color-surface);
      border-radius: var(--border-radius);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      margin-top: -100px;
      border: 1px solid var(--color-border);
      display: grid;
      grid-template-columns: 250px 1fr;
      gap: var(--spacing-lg);
      animation: fadeIn 0.5s ease;
    }

    .movie-poster {
      width: 100%;
      border-radius: var(--border-radius);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
      transform: translateY(-50px);
    }

    .movie-info {
      padding-top: var(--spacing-md);
    }

    .movie-title {
      font-size: 2.5rem;
      margin-bottom: var(--spacing-xs);
      color: var(--color-text);
    }

    .movie-meta {
      display: flex;
      gap: var(--spacing-md);
      margin-bottom: var(--spacing-md);
      color: var(--color-text-secondary);
      flex-wrap: wrap;
    }

    .movie-meta-item {
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
    }

    .movie-rating {
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .rating-star {
      color: var(--color-rating);
    }

    .movie-description {
      margin-bottom: var(--spacing-md);
      line-height: 1.6;
    }

    .movie-actions {
      display: flex;
      gap: var(--spacing-md);
      margin-bottom: var(--spacing-lg);
      flex-wrap: wrap;
    }

    .btn {
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--border-radius);
      font-weight: 600;
      transition: var(--transition);
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-xs);
      text-decoration: none;
    }

    .btn-primary {
      background-color: var(--color-primary);
      color: var(--color-text);
      border: 2px solid var(--color-primary);
    }

    .btn-primary:hover {
      background-color: var(--color-primary-dark);
      border-color: var(--color-primary-dark);
    }

    .btn-secondary {
      background-color: transparent;
      color: var(--color-text);
      border: 2px solid var(--color-text-secondary);
    }

    .btn-secondary:hover {
      background-color: rgba(255, 255, 255, 0.1);
      border-color: var(--color-text);
    }

    /* Trailer Section */
    .trailer-section {
      margin-bottom: var(--spacing-lg);
    }

    .section-title {
      font-size: 1.8rem;
      margin-bottom: var(--spacing-md);
      color: var(--color-primary);
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
    }

    .trailer-container {
      position: relative;
      width: 100%;
      padding-bottom: 56.25%; /* 16:9 aspect ratio */
      overflow: hidden;
      border-radius: var(--border-radius);
    }

    .trailer-container iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: none;
    }

    /* Showtimes Section */
    .showtimes-section {
      margin-bottom: var(--spacing-lg);
    }

    .date-selector {
      display: flex;
      gap: var(--spacing-xs);
      margin-bottom: var(--spacing-md);
      overflow-x: auto;
      padding-bottom: var(--spacing-sm);
    }

    .date-selector::-webkit-scrollbar {
      height: 8px;
    }

    .date-selector::-webkit-scrollbar-track {
      background: var(--color-surface-light);
      border-radius: 10px;
    }

    .date-selector::-webkit-scrollbar-thumb {
      background: var(--color-primary);
      border-radius: 10px;
    }

    .date-item {
      min-width: 100px;
      padding: var(--spacing-sm);
      background-color: var(--color-surface-light);
      border-radius: var(--border-radius);
      text-align: center;
      cursor: pointer;
      transition: var(--transition);
      border: 1px solid var(--color-border);
    }

    .date-item.active {
      background-color: var(--color-primary);
      border-color: var(--color-primary);
    }

    .date-item:hover:not(.active) {
      background-color: rgba(227, 24, 55, 0.1);
      border-color: var(--color-primary);
    }

    .date-day {
      font-size: 1.2rem;
      font-weight: 600;
    }

    .date-date {
      font-size: 0.8rem;
      color: var(--color-text-secondary);
    }

    .date-item.active .date-date {
      color: var(--color-text);
    }

    .showtimes-container {
      display: none;
    }

    .showtimes-container.active {
      display: block;
      animation: fadeIn 0.3s ease;
    }

    .showtime-items {
      display: flex;
      flex-wrap: wrap;
      gap: var(--spacing-sm);
    }

    .showtime-item {
      padding: var(--spacing-sm) var(--spacing-md);
      background-color: var(--color-surface-light);
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: var(--transition);
      border: 1px solid var(--color-border);
    }

    .showtime-item:hover {
      background-color: var(--color-primary);
      border-color: var(--color-primary);
    }

    /* Reviews Section */
    .reviews-section {
      margin-bottom: var(--spacing-lg);
    }

    .reviews-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: var(--spacing-md);
    }

    .review-card {
      background-color: var(--color-surface-light);
      border-radius: var(--border-radius);
      padding: var(--spacing-md);
      border: 1px solid var(--color-border);
    }

    .review-header {
      display: flex;
      align-items: center;
      gap: var(--spacing-sm);
      margin-bottom: var(--spacing-sm);
    }

    .reviewer-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-color: var(--color-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 1.2rem;
    }

    .reviewer-info {
      flex: 1;
    }

    .reviewer-name {
      font-weight: 600;
    }

    .review-date {
      font-size: 0.8rem;
      color: var(--color-text-secondary);
    }

    .review-rating {
      display: flex;
      gap: 2px;
      margin-top: 4px;
    }

    .review-text {
      line-height: 1.6;
      margin-bottom: var(--spacing-sm);
    }

    .write-review-btn {
      display: block;
      width: 100%;
      text-align: center;
      padding: var(--spacing-sm);
      background-color: transparent;
      border: 2px dashed var(--color-border);
      border-radius: var(--border-radius);
      color: var(--color-text-secondary);
      margin-top: var(--spacing-md);
      transition: var(--transition);
      cursor: pointer;
      text-decoration: none;
    }

    .write-review-btn:hover {
      border-color: var(--color-primary);
      color: var(--color-primary);
    }

    /* Similar Movies Section */
    .similar-section {
      margin-bottom: var(--spacing-lg);
    }

    .similar-movies {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: var(--spacing-md);
    }

    .similar-movie {
      border-radius: var(--border-radius);
      overflow: hidden;
      transition: var(--transition);
      border: 1px solid var(--color-border);
    }

    .similar-movie:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
      border-color: var(--color-primary);
    }

    .similar-movie-poster {
      width: 100%;
      aspect-ratio: 2/3;
      object-fit: cover;
    }

    .similar-movie-info {
      padding: var(--spacing-sm);
      text-align: center;
    }

    .similar-movie-title {
      font-weight: 600;
      margin-bottom: 4px;
      font-size: 0.9rem;
    }

    .similar-movie-year {
      font-size: 0.8rem;
      color: var(--color-text-secondary);
    }

    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    /* Responsive Design */
    @media (max-width: 992px) {
      .movie-info-container {
        grid-template-columns: 200px 1fr;
        gap: var(--spacing-md);
      }

      .movie-title {
        font-size: 2rem;
      }
    }

    @media (max-width: 768px) {
      .movie-info-container {
        grid-template-columns: 1fr;
        padding: var(--spacing-md);
      }

      .movie-poster {
        width: 70%;
        max-width: 250px;
        margin: 0 auto;
        transform: translateY(-100px);
      }

      .movie-info {
        margin-top: -80px;
      }

      .movie-actions {
        justify-content: center;
      }

      .date-selector {
        justify-content: center;
      }
    }

    @media (max-width: a480px) {
      .movie-title {
        font-size: 1.8rem;
      }

      .movie-meta {
        flex-direction: column;
        gap: var(--spacing-xs);
      }

      .movie-actions {
        flex-direction: column;
      }

      .btn {
        width: 100%;
        text-align: center;
        justify-content: center;
      }
    }
</style>
@endsection

@section('content')
  <div class="movie-detail">
    <div class="movie-hero"></div>

    <div class="movie-info-container">
      <div>
        <img src="{{ asset('storage/' . $movie->poster_path) }}" alt="{{ $movie->title }}" class="movie-poster">
      </div>

      <div class="movie-info">
        <h1 class="movie-title">{{ $movie->title }}</h1>

        <div class="movie-meta">
          <div class="movie-meta-item">
            <i class="fas fa-calendar-alt"></i>
            <span>{{ $movie->release_date->format('Y') }}</span>
          </div>

          <div class="movie-meta-item">
            <i class="fas fa-clock"></i>
            <span>{{ $movie->duration }}</span>
          </div>

          <div class="movie-meta-item">
            <i class="fas fa-film"></i>
            <span>{{ $movie->genre }}</span>
          </div>

          <div class="movie-meta-item movie-rating">
            <i class="fas fa-star rating-star"></i>
            <span>{{ $movie->rating }}</span>
          </div>
        </div>

        <p class="movie-description">{{ $movie->description }}</p>

        <div class="movie-actions">
          <a href="{{ route('bookings.book-movie', $movie->id) }}" class="btn btn-primary">
            <i class="fas fa-ticket-alt"></i> Book Tickets
          </a>

          <a href="#trailer" class="btn btn-secondary">
            <i class="fas fa-play"></i> Watch Trailer
          </a>
        </div>

        <div id="trailer" class="trailer-section">
          <h2 class="section-title"><i class="fas fa-film"></i> Trailer</h2>
          <div class="trailer-container">
            @php
              // Extract YouTube video ID from trailer URL
              $trailerUrl = $movie->trailer_url;
              $videoId = '';

              if (preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $trailerUrl, $match)) {
                $videoId = $match[1];
              }
            @endphp

            @if($videoId)
              <iframe src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen></iframe>
            @else
              <div style="display: flex; align-items: center; justify-content: center; height: 100%; background-color: var(--color-surface-light);">
                <p>Trailer not available</p>
              </div>
            @endif
          </div>
        </div>

        <div class="showtimes-section">
          <h2 class="section-title"><i class="fas fa-calendar-day"></i> Showtimes</h2>

          <div class="date-selector">
            @php
              $dates = [];
              $today = \Carbon\Carbon::now();

              for ($i = 0; $i < 7; $i++) {
                $date = $today->copy()->addDays($i);
                $dates[] = $date;
              }
            @endphp

            @foreach($dates as $index => $date)
              <div class="date-item {{ $index === 0 ? 'active' : '' }}" data-date="{{ $date->format('Y-m-d') }}">
                <div class="date-day">{{ $date->format('D') }}</div>
                <div class="date-date">{{ $date->format('M d') }}</div>
              </div>
            @endforeach
          </div>

          @foreach($dates as $index => $date)
            <div class="showtimes-container {{ $index === 0 ? 'active' : '' }}" id="showtimes-{{ $date->format('Y-m-d') }}">
              <div class="showtime-items">
                @php
                  $showtimes = $movie->showtimes->where('date', $date->format('Y-m-d'));
                @endphp

                @if($showtimes->count() > 0)
                  @foreach($showtimes as $showtime)
                    <a href="{{ route('bookings.book-movie', ['movie' => $movie->id, 'showtime' => $showtime->id]) }}" class="showtime-item">
                      {{ \Carbon\Carbon::parse($showtime->time)->format('h:i A') }}
                    </a>
                  @endforeach
                @else
                  <p>No showtimes available for this date.</p>
                @endif
              </div>
            </div>
          @endforeach
        </div>

        <div class="reviews-section">
          <h2 class="section-title"><i class="fas fa-star"></i> Reviews</h2>

          <div class="reviews-container">
            <!-- Sample reviews - in a real app, these would come from the database -->
            <div class="review-card">
              <div class="review-header">
                <div class="reviewer-avatar">J</div>
                <div class="reviewer-info">
                  <div class="reviewer-name">John Doe</div>
                  <div class="review-date">2 days ago</div>
                  <div class="review-rating">
                    <i class="fas fa-star rating-star"></i>
                    <i class="fas fa-star rating-star"></i>
                    <i class="fas fa-star rating-star"></i>
                    <i class="fas fa-star rating-star"></i>
                    <i class="far fa-star rating-star"></i>
                  </div>
                </div>
              </div>
              <p class="review-text">Great movie! The plot was engaging and the characters were well-developed.</p>
            </div>

            <div class="review-card">
              <div class="review-header">
                <div class="reviewer-avatar">S</div>
                <div class="reviewer-info">
                  <div class="reviewer-name">Sarah Smith</div>
                  <div class="review-date">1 week ago</div>
                  <div class="review-rating">
                    <i class="fas fa-star rating-star"></i>
                    <i class="fas fa-star rating-star"></i>
                    <i class="fas fa-star rating-star"></i>
                    <i class="fas fa-star rating-star"></i>
                    <i class="fas fa-star rating-star"></i>
                  </div>
                </div>
              </div>
              <p class="review-text">Absolutely loved it! The cinematography was stunning and the acting was superb.</p>
            </div>

            <a href="#" class="write-review-btn">
              <i class="fas fa-plus"></i> Write a Review
            </a>
          </div>
        </div>

        <div class="similar-section">
          <h2 class="section-title"><i class="fas fa-film"></i> Similar Movies</h2>

          <div class="similar-movies">
            <!-- Sample similar movies - in a real app, these would come from the database -->
            @foreach($movies = \App\Models\Movie::where('genre', 'like', '%' . explode(',', $movie->genre)[0] . '%')->where('id', '!=', $movie->id)->take(4)->get() as $similarMovie)
              <a href="{{ route('movies.show', $similarMovie->id) }}" class="similar-movie">
                <img src="{{ asset('storage/' . $similarMovie->poster_path) }}" alt="{{ $similarMovie->title }}" class="similar-movie-poster">
                <div class="similar-movie-info">
                  <div class="similar-movie-title">{{ $similarMovie->title }}</div>
                  <div class="similar-movie-year">{{ \Carbon\Carbon::parse($similarMovie->release_date)->format('Y') }}</div>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  // Showtime date selector
  document.addEventListener('DOMContentLoaded', function() {
    const dateItems = document.querySelectorAll('.date-item');

    dateItems.forEach(item => {
      item.addEventListener('click', function() {
        // Remove active class from all date items and showtime containers
        dateItems.forEach(date => date.classList.remove('active'));
        document.querySelectorAll('.showtimes-container').forEach(container => {
          container.classList.remove('active');
        });

        // Add active class to clicked date item
        this.classList.add('active');

        // Show corresponding showtimes
        const date = this.getAttribute('data-date');
        document.getElementById(`showtimes-${date}`).classList.add('active');
      });
    });
  });
</script>
@endsection
