@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<style>
    .dashboard {
        padding-top: 120px;
        padding-bottom: var(--spacing-lg);
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
        min-height: calc(100vh - 200px);
        background-color: #000000;
    }

    .dashboard-header {
        margin-bottom: 3rem;
        text-align: center;
        position: relative;
    }

    .dashboard-title {
        font-size: 3.5rem;
        color: #E31837;
        margin-bottom: 1rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-shadow: 2px 2px 4px rgba(227, 24, 55, 0.3);
    }

    .dashboard-subtitle {
        font-size: 1.4rem;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 300;
    }

    .dashboard-sections {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .dashboard-section {
        background: rgba(26, 26, 26, 0.95);
        border-radius: 15px;
        padding: 2rem;
        border: 1px solid #333;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .dashboard-section:hover {
        border-color: #E31837;
        box-shadow: 0 10px 30px rgba(227, 24, 55, 0.2);
        transform: translateY(-5px);
    }

    .section-title {
        font-size: 1.8rem;
        color: #E31837;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid #333;
        padding-bottom: 1rem;
    }

    .section-title i {
        font-size: 1.5rem;
        color: #E31837;
    }

    .booking-card {
        background: rgba(42, 42, 42, 0.95);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid #333;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .booking-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent, rgba(227, 24, 55, 0.1), transparent);
        animation: shine 2s infinite;
    }

    @keyframes shine {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .booking-card:hover {
        transform: translateY(-5px);
        border-color: #E31837;
        box-shadow: 0 5px 15px rgba(227, 24, 55, 0.15);
    }

    .booking-movie {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1rem;
        position: relative;
    }

    .movie-poster {
        width: 120px;
        height: 180px;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .booking-card:hover .movie-poster {
        transform: scale(1.05);
        box-shadow: 0 6px 12px rgba(227, 24, 55, 0.2);
    }

    .movie-info {
        flex: 1;
    }

    .movie-title {
        font-size: 1.4rem;
        color: #ffffff;
        margin-bottom: 0.8rem;
        font-weight: 600;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .booking-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .detail-label {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .detail-value {
        font-size: 1.1rem;
        color: #ffffff;
        font-weight: 500;
    }

    .seats-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .seat-tag {
        background: #E31837;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 500;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .seat-tag:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 4px rgba(227, 24, 55, 0.3);
    }

    .no-bookings {
        text-align: center;
        padding: 3rem 2rem;
        color: rgba(255, 255, 255, 0.5);
        font-style: italic;
        font-size: 1.1rem;
        background: rgba(42, 42, 42, 0.5);
        border-radius: 10px;
        border: 1px dashed #333;
    }

    @media (max-width: 768px) {
        .dashboard {
            padding-top: 100px;
            width: 95%;
        }

        .dashboard-title {
            font-size: 2.5rem;
        }

        .movie-poster {
            width: 100px;
            height: 150px;
        }

        .booking-details {
            grid-template-columns: 1fr;
        }

        .section-title {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .dashboard-title {
            font-size: 2rem;
        }

        .booking-movie {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .movie-poster {
            margin-bottom: 1rem;
        }

        .booking-details {
            text-align: center;
        }

        .seats-list {
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="dashboard">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Welcome, {{ auth()->user()->name }}!</h1>
        <p class="dashboard-subtitle">Manage your movie experiences</p>
    </div>

    <div class="dashboard-sections">
        <!-- Upcoming Reservations Section -->
        <div class="dashboard-section">
            <h2 class="section-title">
                <i class="fas fa-calendar-alt"></i>
                Upcoming Reservations
            </h2>
            @if($upcomingBookings->count() > 0)
                @foreach($upcomingBookings as $booking)
                    <div class="booking-card">
                        <div class="booking-movie">
                            <img src="{{ asset($booking->movie->poster_url) }}" alt="{{ $booking->movie->title }}" class="movie-poster">
                            <div class="movie-info">
                                <h3 class="movie-title">{{ $booking->movie->title }}</h3>
                                <div class="booking-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Date</span>
                                        <span class="detail-value">{{ $booking->showtime->formatted_date }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Time</span>
                                        <span class="detail-value">{{ $booking->showtime->formatted_time }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Seats</span>
                                        <div class="seats-list">
                                            @foreach($booking->seats as $seat)
                                                <span class="seat-tag">{{ $seat->row }}{{ $seat->number }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-bookings">
                    <p>No upcoming reservations found.</p>
                </div>
            @endif
        </div>

        <!-- Booking History Section -->
        <div class="dashboard-section">
            <h2 class="section-title">
                <i class="fas fa-history"></i>
                Booking History
            </h2>
            @if($bookingHistory->count() > 0)
                @foreach($bookingHistory as $booking)
                    <div class="booking-card">
                        <div class="booking-movie">
                            <img src="{{ asset($booking->movie->poster_url) }}" alt="{{ $booking->movie->title }}" class="movie-poster">
                            <div class="movie-info">
                                <h3 class="movie-title">{{ $booking->movie->title }}</h3>
                                <div class="booking-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Date</span>
                                        <span class="detail-value">{{ $booking->showtime->formatted_date }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Time</span>
                                        <span class="detail-value">{{ $booking->showtime->formatted_time }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Seats</span>
                                        <div class="seats-list">
                                            @foreach($booking->seats as $seat)
                                                <span class="seat-tag">{{ $seat->row }}{{ $seat->number }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-bookings">
                    <p>No booking history found.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 