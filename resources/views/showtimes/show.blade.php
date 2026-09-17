@extends('layouts.admin')

@section('title', 'Showtime Details')

@section('content')
<div style="padding-top:120px;max-width:600px;margin:0 auto;width:90%;min-height:80vh;color:#fff;">
    <h1 style="color:#E31837;">Showtime #{{ $showtime->id }}</h1>
    <div style="background:#1a1a1a;border:1px solid #333;border-radius:8px;padding:1.5rem;margin-top:1rem;">
        <p><strong>Movie:</strong> {{ optional($showtime->movie)->title }}</p>
        <p><strong>Date:</strong> {{ $showtime->date->format('M d, Y') }}</p>
        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($showtime->time)->format('h:i A') }}</p>
    </div>
    <div style="margin-top:1rem;">
        <a href="{{ route('admin.showtimes.edit', $showtime->id) }}" style="background:#E31837;color:#fff;padding:0.6rem 1.2rem;border-radius:8px;text-decoration:none;">Edit</a>
        <a href="{{ route('admin.showtimes.index') }}" style="color:#fff;margin-left:1rem;">Back</a>
    </div>
</div>
@endsection
