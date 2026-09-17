@extends('layouts.app')

@section('title', 'New Booking')

@section('content')
<div style="padding-top:120px;max-width:800px;margin:0 auto;width:90%;min-height:80vh;color:#fff;">
    <h1 style="color:#E31837;margin-bottom:1rem;">Create Booking</h1>
    <p style="color:#ccc;">Select a movie to start booking:</p>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-top:1rem;">
        @foreach($movies as $movie)
        <a href="{{ route('bookings.book-movie', $movie->id) }}" style="background:#1a1a1a;border:1px solid #333;border-radius:8px;padding:1rem;color:#fff;text-decoration:none;text-align:center;">{{ $movie->title }}</a>
        @endforeach
    </div>
</div>
@endsection
