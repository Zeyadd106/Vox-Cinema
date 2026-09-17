@extends('layouts.admin')

@section('title', 'Showtimes')

@section('content')
<div style="padding-top:120px;max-width:1000px;margin:0 auto;width:90%;min-height:80vh;color:#fff;">
    <h1 style="color:#E31837;margin-bottom:1rem;">Showtimes</h1>
    <a href="{{ route('admin.showtimes.create') }}" style="background:#E31837;color:#fff;padding:0.6rem 1.2rem;border-radius:8px;text-decoration:none;">Add Showtime</a>
    <table style="width:100%;margin-top:1rem;border-collapse:collapse;background:#1a1a1a;">
        <thead><tr style="color:#ccc;text-align:left;"><th style="padding:0.75rem;">ID</th><th>Movie</th><th>Date</th><th>Time</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($showtimes as $showtime)
            <tr style="border-top:1px solid #333;">
                <td style="padding:0.75rem;">#{{ $showtime->id }}</td>
                <td>{{ optional($showtime->movie)->title }}</td>
                <td>{{ $showtime->date->format('M d, Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($showtime->time)->format('h:i A') }}</td>
                <td><a href="{{ route('admin.showtimes.show', $showtime->id) }}" style="color:#E31837;">View</a> | <a href="{{ route('admin.showtimes.edit', $showtime->id) }}" style="color:#E31837;">Edit</a></td>
            </tr>
        @empty
            <tr><td colspan="5" style="padding:1rem;">No showtimes.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
