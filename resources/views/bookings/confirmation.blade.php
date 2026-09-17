@extends('layouts.app')

@section('title', 'Booking Confirmation | Vox Cinemas')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    :root {
      --color-primary: #E31837;
      --color-primary-dark: #c41530;
      --color-primary-light: #ff1f3d;
      --color-background: #000000;
      --color-surface: #1a1a1a;
      --color-surface-light: #2a2a2a;
      --color-text: #ffffff;
      --color-text-secondary: #cccccc;
      --color-border: #333333;
      --color-success: #4CAF50;
      --color-warning: #FFC107;
      --color-error: #f44336;

      --spacing-xs: 0.5rem;
      --spacing-sm: 1rem;
      --spacing-md: 2rem;
      --spacing-lg: 3rem;

      --border-radius: 8px;
      --transition: all 0.3s ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: var(--color-background);
      color: var(--color-text);
      min-height: 100vh;
      padding-bottom: 60px;
      line-height: 1.6;
    }

    /* Confirmation Section */
    .confirmation {
      padding: 8rem var(--spacing-md) var(--spacing-md);
      max-width: 800px;
      margin: 0 auto;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .confirmation-card {
      background-color: var(--color-surface);
      padding: var(--spacing-lg);
      border-radius: var(--border-radius);
      border: 1px solid var(--color-border);
      text-align: center;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      transition: var(--transition);
    }

    .confirmation-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(227, 24, 55, 0.2);
    }

    .success-icon {
      color: var(--color-success);
      font-size: 4rem;
      margin-bottom: var(--spacing-md);
      animation: scaleIn 0.5s ease 0.2s both;
    }

    @keyframes scaleIn {
      from {
        transform: scale(0);
      }
      to {
        transform: scale(1);
      }
    }

    .confirmation h1 {
      font-size: 2.5rem;
      color: var(--color-primary);
      margin-bottom: var(--spacing-lg);
      text-transform: uppercase;
      letter-spacing: 1px;
      animation: slideIn 0.5s ease 0.3s both;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateX(-20px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .confirmation p {
      color: var(--color-text-secondary);
      margin-bottom: var(--spacing-md);
      font-size: 1.1rem;
      animation: fadeIn 0.5s ease 0.4s both;
    }

    /* Ticket Details */
    .ticket {
      background-color: var(--color-surface-light);
      padding: var(--spacing-lg);
      border-radius: var(--border-radius);
      margin: var(--spacing-lg) 0;
      border: 1px solid var(--color-border);
      position: relative;
      overflow: hidden;
      animation: slideUp 0.5s ease 0.5s both;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .ticket::before {
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
      0% {
        transform: translateX(-100%);
      }
      100% {
        transform: translateX(100%);
      }
    }

    .ticket h2 {
      color: var(--color-primary);
      font-size: 1.5rem;
      margin-bottom: var(--spacing-md);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: var(--spacing-sm);
    }

    .ticket-info {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: var(--spacing-md);
      text-align: left;
      margin-bottom: var(--spacing-md);
    }

    .ticket-item {
      padding: var(--spacing-sm);
      background-color: var(--color-surface);
      border-radius: var(--border-radius);
      border: 1px solid var(--color-border);
      transition: var(--transition);
    }

    .ticket-item:hover {
      transform: translateY(-2px);
      border-color: var(--color-primary);
      box-shadow: 0 5px 15px rgba(227, 24, 55, 0.1);
    }

    .ticket-item span {
      display: block;
    }

    .ticket-item span:first-child {
      color: var(--color-text-secondary);
      font-size: 0.9rem;
      margin-bottom: var(--spacing-xs);
    }

    .ticket-item span:last-child {
      color: var(--color-text);
      font-size: 1.1rem;
      font-weight: 500;
    }

    /* QR Code Section */
    .qr-section {
      text-align: center;
      margin: var(--spacing-lg) 0;
      animation: fadeIn 0.5s ease 0.6s both;
    }

    .qr-container {
      background-color: var(--color-text);
      padding: var(--spacing-md);
      border-radius: var(--border-radius);
      display: inline-block;
      margin: var(--spacing-md) auto;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
      transition: var(--transition);
    }

    .qr-container:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 25px rgba(227, 24, 55, 0.2);
    }

    .qr-code {
      width: 200px;
      height: 200px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .qr-code img {
      max-width: 100%;
      height: auto;
      display: block;
    }

    .qr-instructions {
      color: var(--color-text-secondary);
      font-size: 0.9rem;
      margin-top: var(--spacing-sm);
    }

    /* Buttons */
    .btn-group {
      display: flex;
      gap: var(--spacing-md);
      justify-content: center;
      margin-top: var(--spacing-lg);
      animation: fadeIn 0.5s ease 0.7s both;
    }

    .btn {
      background-color: var(--color-primary);
      color: var(--color-text);
      padding: var(--spacing-sm) var(--spacing-md);
      border: 2px solid var(--color-primary);
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: var(--transition);
      font-size: 1.1rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      text-decoration: none;
      min-width: 200px;
      text-align: center;
    }

    .btn:hover {
      background-color: transparent;
      color: var(--color-primary);
      transform: translateY(-2px);
    }

    .btn-outline {
      background-color: transparent;
      color: var(--color-primary);
    }

    .btn-outline:hover {
      background-color: var(--color-primary);
      color: var(--color-text);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .confirmation h1 {
        font-size: 2rem;
      }

      .ticket-info {
        grid-template-columns: 1fr;
      }

      .btn-group {
        flex-direction: column;
      }

      .btn {
        width: 100%;
      }

      .qr-code {
        width: 150px;
        height: 150px;
      }
    }

    @media (max-width: 480px) {
      .confirmation-card {
        padding: var(--spacing-md);
      }

      .success-icon {
        font-size: 3rem;
      }

      .confirmation h1 {
        font-size: 1.8rem;
      }

      .ticket {
        padding: var(--spacing-md);
      }

      .ticket h2 {
        font-size: 1.3rem;
      }
    }

    /* Accessibility */
    @media (prefers-reduced-motion: reduce) {
      * {
        animation: none !important;
        transition: none !important;
      }
    }

    /* Header */
    .header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      background: rgba(26, 26, 26, 0.95);
      padding: var(--spacing-sm) 6%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 6px rgba(227, 24, 55, 0.2);
      z-index: 1000;
      border-bottom: 1px solid var(--color-border);
      backdrop-filter: blur(10px);
    }

    .header .logo {
      font-size: 2rem;
      color: var(--color-primary);
      font-weight: bold;
      text-decoration: none;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .navbar {
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
    }

    .header .navbar a {
      font-size: 1.1rem;
      color: var(--color-text);
      text-decoration: none;
      transition: var(--transition);
      position: relative;
      font-weight: 500;
    }

    .header .navbar a::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background-color: var(--color-primary);
      transition: var(--transition);
    }

    .header .navbar a:hover::after {
      width: 100%;
    }

    .menu-btn {
      display: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: var(--color-text);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .menu-btn {
        display: block;
      }

      .navbar {
        position: fixed;
        top: 0;
        right: -100%;
        width: 70%;
        height: 100vh;
        background-color: var(--color-surface);
        flex-direction: column;
        justify-content: center;
        padding: var(--spacing-lg);
        transition: var(--transition);
      }

      .navbar.active {
        right: 0;
      }

      .header .navbar a {
        margin: var(--spacing-sm) 0;
        font-size: 1.2rem;
      }
    }

    @media (max-width: 480px) {
      .header {
        padding: var(--spacing-sm) 4%;
      }

      .header .logo {
        font-size: 1.5rem;
      }
    }
</style>
@endsection

@section('content')
  <!-- Header -->
  <header class="header">
    <a href="{{ route('home') }}" class="logo">Vox Cinemas</a>
    <div class="menu-btn">☰</div>
    <nav class="navbar">
      <a href="{{ route('home') }}">Home</a>
      <a href="{{ route('coming-soon') }}">Coming Soon</a>
      <a href="{{ route('movies.index') }}">Movies</a>
      @guest
        <a href="{{ route('login') }}">Login</a>
        <a href="{{ route('register') }}">Register</a>
      @else
        <a href="{{ route('bookings.index') }}">My Bookings</a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
        </form>
      @endguest
    </nav>
  </header>

  <section class="confirmation">
    <div class="confirmation-card">
        <i class="fas fa-check-circle success-icon"></i>
        <h1>Booking Confirmed!</h1>
        <p>Thank you for your purchase. Your tickets have been booked successfully.</p>

        <div class="ticket">
            <h2><i class="fas fa-ticket-alt"></i> Your Ticket Details</h2>
            <div class="ticket-info">
                <div class="ticket-item">
                    <span>Movie</span>
                    <span>{{ $booking->showtime->movie->title }}</span>
                </div>
                <div class="ticket-item">
                    <span class="label">Date</span>
                    <span class="value">{{ $booking->showtime->formatted_date }}</span>
                </div>
                <div class="ticket-item">
                    <span class="label">Time</span>
                    <span class="value">{{ $booking->showtime->formatted_time }}</span>
                </div>
                <div class="ticket-item">
                    <span>Seats</span>
                    <span>{{ $booking->seats->map(function($seat) { return $seat->row . $seat->number; })->join(', ') }}</span>
                </div>
                <div class="ticket-item">
                    <span>Booking ID</span>
                    <span>{{ $booking->booking_reference }}</span>
                </div>
                <div class="ticket-item">
                    <span>Total Amount</span>
                    <span>${{ number_format($booking->total_price, 2) }}</span>
                </div>
            </div>

            <div class="qr-section">
                <h3>Scan for Entry</h3>
                <div class="qr-container">
                    <div id="qr-code" class="qr-code"></div>
                </div>
                <p class="qr-instructions">Please show this QR code at the cinema entrance</p>
            </div>
        </div>

        <div class="btn-group">
            <a href="{{ url('/') }}" class="btn btn-outline">Back to Home</a>
            <button class="btn" onclick="window.print()">Download Ticket</button>
        </div>
    </div>
  </section>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Check if QR code library is loaded
    if (typeof QRCode === 'undefined') {
        console.error('QR Code library not loaded');
        document.getElementById('qr-code').innerHTML = '<p style="color: var(--color-error);">Unable to load QR code generator. Please check your internet connection and refresh the page.</p>';
    } else {
        document.addEventListener('DOMContentLoaded', () => {
            // Clear any existing content in the QR code container
            const qrContainer = document.getElementById('qr-code');
            qrContainer.innerHTML = '';

            // Generate QR Code with error handling
            try {
                const qrData = {
                    bookingId: '{{ $booking->booking_reference }}',
                    movie: '{{ addslashes($booking->showtime->movie->title) }}',
                    date: '{{ $booking->showtime->formatted_date }}',
                    time: '{{ $booking->showtime->formatted_time }}',
                    seats: {!! json_encode($booking->seats->map(function($seat) { return $seat->row . $seat->number; })) !!}
                };

                new QRCode(qrContainer, {
                    text: JSON.stringify(qrData),
                    width: 200,
                    height: 200,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                });

                // Add fade-in animation to QR code
                const qrImg = qrContainer.querySelector('img');
                if (qrImg) {
                    qrImg.style.opacity = '0';
                    qrImg.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => {
                        qrImg.style.opacity = '1';
                    }, 100);
                }
            } catch (error) {
                console.error('Error generating QR code:', error);
                qrContainer.innerHTML = '<p style="color: var(--color-error);">Error generating QR code. Please try refreshing the page.</p>';
            }
        });
    }

    // Mobile menu toggle
    document.querySelector('.menu-btn').addEventListener('click', () => {
        document.querySelector('.navbar').classList.toggle('active');
    });
</script>
@endsection
