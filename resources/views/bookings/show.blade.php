@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div style="padding-top:120px;max-width:800px;margin:0 auto;width:90%;min-height:80vh;color:#fff;">
    <h1 style="color:#E31837;margin-bottom:1rem;">Booking {{ $booking->booking_reference }}</h1>
    <div style="background:#1a1a1a;border:1px solid #333;border-radius:8px;padding:1.5rem;">
        <p><strong>Movie:</strong> {{ optional(optional($booking->showtime)->movie)->title }}</p>
        <p><strong>Date:</strong> {{ optional($booking->showtime)->date?->format('M d, Y') }}</p>
        <p><strong>Time:</strong> {{ optional($booking->showtime)->time ? \Carbon\Carbon::parse($booking->showtime->time)->format('h:i A') : '' }}</p>
        <p><strong>Total:</strong> ${{ $booking->total_price }}</p>
        <p><strong>Status:</strong> {{ $booking->status }}</p>
        <p><strong>Payment:</strong> {{ $booking->payment_status }}</p>
        <p><strong>Seats:</strong> {{ $booking->seats->pluck('seat_number')->filter()->implode(', ') ?: $booking->seats->count() . ' seat(s)' }}</p>
    </div>
    <div style="margin-top:1.5rem;">
        @if($booking->payment_status !== 'paid')
        <a href="{{ route('payments.create', $booking->id) }}" style="background:#E31837;color:#fff;padding:0.6rem 1.2rem;border-radius:8px;text-decoration:none;">Pay Now</a>
        @else
        <a href="{{ route('bookings.confirmation', $booking->id) }}" style="background:#E31837;color:#fff;padding:0.6rem 1.2rem;border-radius:8px;text-decoration:none;">View Confirmation</a>
        @endif
        <a href="{{ route('bookings.index') }}" style="color:#fff;margin-left:1rem;">Back</a>
    </div>
</div>
@endsection
