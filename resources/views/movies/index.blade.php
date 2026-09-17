@extends('layouts.app')

@section('title', 'Movies')

@section('content')
<div style="padding-top:120px;max-width:1200px;margin:0 auto;width:90%;min-height:80vh;">
    <h1 style="color:#E31837;text-align:center;margin-bottom:2rem;">Now Showing</h1>
    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:1rem;border-radius:8px;margin-bottom:1rem;">{{ session('success') }}</div>
    @endif
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:2rem;">
        @forelse($currentMovies ?? $movies as $movie)
        <article style="background:#1a1a1a;border:1px solid #333;border-radius:8px;overflow:hidden;">
            @if($movie->poster_path)
            <img src="{{ asset('storage/' . $movie->poster_path) }}" alt="{{ $movie->title }}" style="width:100%;aspect-ratio:2/3;object-fit:cover;">
            @endif
            <div style="padding:1rem;text-align:center;">
                <h3><a href="{{ route('movies.show', $movie->id) }}" style="color:#fff;text-decoration:none;">{{ $movie->title }}</a></h3>
                <p style="color:#ccc;font-size:0.9rem;">{{ $movie->genre }} | {{ $movie->rating }}</p>
                <a href="{{ route('bookings.book-movie', $movie->id) }}" style="display:inline-block;margin-top:0.5rem;background:#E31837;color:#fff;padding:0.5rem 1rem;border-radius:8px;text-decoration:none;">Book Now</a>
            </div>
        </article>
        @empty
        <p style="color:#fff;">No movies available.</p>
        @endforelse
    </div>
</div>
@endsection
