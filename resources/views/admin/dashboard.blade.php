@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<section id="dashboard" class="section active">
    <h1>Dashboard Overview</h1>
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Movies</h3>
            <p>{{ $stats['total_movies'] }}</p>
        </div>
        <div class="stat-card">
            <h3>Active Bookings</h3>
            <p>{{ $stats['total_bookings'] }}</p>
        </div>
        <div class="stat-card">
            <h3>Total Users</h3>
            <p>{{ $stats['total_users'] }}</p>
        </div>
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <p>${{ number_format($stats['revenue'], 2) }}</p>
        </div>
    </div>

    <div class="form-container">
        <h2 class="text-xl font-semibold mb-4 text-white border-b border-gray-700 pb-2">Recent Bookings</h2>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User</th>
                    <th>Movie</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['recent_bookings'] as $booking)
                    <tr>
                        <td>#{{ $booking->id }}</td>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->showtime->movie->title }}</td>
                        <td>
                            {{ $booking->showtime->formatted_date }}<br>
                            <span class="text-sm text-gray-400">{{ $booking->showtime->formatted_time }}</span>
                        </td>
                        <td>
                            <span class="px-2 py-1 rounded text-xs 
                                {{ $booking->payment_status === 'paid' ? 'bg-green-500' : 
                                   ($booking->payment_status === 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </td>
                        <td>${{ number_format($booking->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
