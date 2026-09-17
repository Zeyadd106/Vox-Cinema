@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div style="padding-top:120px;max-width:1000px;margin:0 auto;width:90%;min-height:80vh;">
    <h1 style="color:#E31837;text-align:center;margin-bottom:2rem;">My Bookings</h1>
    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:1rem;border-radius:8px;margin-bottom:1rem;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="background:#f8d7da;color:#721c24;padding:1rem;border-radius:8px;margin-bottom:1rem;">{{ session('error') }}</div>
    @endif
    @forelse($bookings as $booking)
    <div style="background:#1a1a1a;border:1px solid #333;border-radius:8px;padding:1rem;margin-bottom:1rem;color:#fff;">
        <strong>#{{ $booking->booking_reference }}</strong> — {{ optional(optional($booking->showtime)->movie)->title ?? 'Movie' }}
        <span style="color:#ccc;">| {{ optional($booking->showtime)->date?->format('M d, Y') }} {{ optional($booking->showtime)->time ? \Carbon\Carbon::parse($booking->showtime->time)->format('h:i A') : '' }}</span>
        <span style="color:#ccc;">| ${{ number_format($booking->total_price, 2) }} | {{ $booking->payment_status }}</span>
        <a href="{{ route('bookings.show', $booking->id) }}" style="color:#E31837;margin-left:1rem;">View</a>
    </div>
    @empty
    <p style="color:#fff;text-align:center;">No bookings yet. <a href="{{ route('movies.list') }}" style="color:#E31837;">Browse movies</a></p>
    @endforelse
</div>
@endsection
