@extends('layouts.admin')

@section('title', 'Edit Showtime')

@section('content')
<div style="padding-top:120px;max-width:600px;margin:0 auto;width:90%;min-height:80vh;color:#fff;">
    <h1 style="color:#E31837;margin-bottom:1rem;">Edit Showtime #{{ $showtime->id }}</h1>
    @if($errors->any())
        <div style="background:#f8d7da;color:#721c24;padding:1rem;border-radius:8px;margin-bottom:1rem;">
            <ul style="margin:0;padding-left:1.2rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.showtimes.update', $showtime->id) }}" style="background:#1a1a1a;padding:1.5rem;border-radius:8px;border:1px solid #333;">
        @csrf
        @method('PUT')
        <label>Movie</label><br>
        <select name="movie_id" style="width:100%;padding:0.5rem;margin:0.5rem 0 1rem;">
            @foreach($movies as $movie)<option value="{{ $movie->id }}" {{ $showtime->movie_id == $movie->id ? 'selected' : '' }}>{{ $movie->title }}</option>@endforeach
        </select><br>
        <label>Date</label><br>
        <input type="date" name="date" value="{{ $showtime->date->format('Y-m-d') }}" style="width:100%;padding:0.5rem;margin:0.5rem 0 1rem;"><br>
        <label>Time</label><br>
        <input type="time" name="time" value="{{ \Carbon\Carbon::parse($showtime->time)->format('H:i') }}" style="width:100%;padding:0.5rem;margin:0.5rem 0 1rem;"><br>
        <button type="submit" style="background:#E31837;color:#fff;padding:0.6rem 1.2rem;border:none;border-radius:8px;">Update</button>
        <a href="{{ route('admin.showtimes.index') }}" style="color:#fff;margin-left:1rem;">Cancel</a>
    </form>
</div>
@endsection
