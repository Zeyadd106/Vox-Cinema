@extends('layouts.admin')

@section('title', $movie->title)

@section('content')
<div style="padding-top:120px;max-width:900px;margin:0 auto;width:90%;min-height:80vh;">
    <h1 style="color:#E31837;margin-bottom:1rem;">{{ $movie->title }}</h1>
    <p style="color:#ccc;">{{ $movie->description }}</p>
    <p style="color:#ccc;margin-top:1rem;">Genre: {{ $movie->genre }} | Rating: {{ $movie->rating }} | Duration: {{ $movie->duration }} min | Status: {{ $movie->status }}</p>
    @if($movie->poster_path)
    <img src="{{ asset('storage/' . $movie->poster_path) }}" alt="{{ $movie->title }}" style="max-width:300px;margin-top:1rem;border-radius:8px;">
    @endif
    <h2 style="color:#fff;margin-top:2rem;">Upcoming Showtimes ({{ $movie->showtimes->count() }})</h2>
    <ul style="color:#ccc;">
        @forelse($movie->showtimes as $showtime)
        <li>{{ $showtime->date->format('M d, Y') }} at {{ \Carbon\Carbon::parse($showtime->time)->format('h:i A') }}</li>
        @empty
        <li>No upcoming showtimes.</li>
        @endforelse
    </ul>
    <div style="margin-top:2rem;">
        <a href="{{ route('admin.movies.edit', $movie->id) }}" style="background:#E31837;color:#fff;padding:0.6rem 1.2rem;border-radius:8px;text-decoration:none;">Edit</a>
        <a href="{{ route('admin.movies.index') }}" style="color:#fff;margin-left:1rem;">Back to list</a>
    </div>
</div>
@endsection
