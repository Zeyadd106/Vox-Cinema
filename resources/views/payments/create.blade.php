@extends('layouts.app')

@section('title', 'Complete Payment')

@section('styles')
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
      --color-available: #00C851;
      --color-unavailable: #ff4444;
      --color-selected: #FFC107;

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
    }

    .payment-page {
      padding-top: 120px;
      padding-bottom: var(--spacing-lg);
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
    }

    .payment-header {
      text-align: center;
      margin-bottom: var(--spacing-lg);
    }

    .payment-title {
      font-size: 2rem;
      color: var(--color-primary);
      margin-bottom: var(--spacing-xs);
    }

    .payment-subtitle {
      font-size: 1.1rem;
      color: var(--color-text-secondary);
    }

    .payment-content {
      display: grid;
      grid-template-columns: 1fr 300px;
      gap: var(--spacing-lg);
    }

    .payment-methods {
      background-color: var(--color-surface);
      border-radius: var(--border-radius);
      padding: var(--spacing-md);
      border: 1px solid var(--color-border);
    }

    .payment-method {
      background-color: var(--color-surface-light);
      border: 1px solid var(--color-border);
      border-radius: var(--border-radius);
      padding: var(--spacing-md);
      margin-bottom: var(--spacing-sm);
      cursor: pointer;
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
    }

    .payment-method:hover {
      border-color: var(--color-primary);
      background-color: rgba(227, 24, 55, 0.1);
    }

    .payment-method.active {
      border-color: var(--color-primary);
      background-color: rgba(227, 24, 55, 0.1);
    }

    .payment-method-icon {
      font-size: 2rem;
      color: var(--color-text);
      width: 40px;
      text-align: center;
    }

    .payment-method-details {
      flex: 1;
    }

    .payment-method-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: var(--spacing-xs);
    }

    .payment-method-description {
      color: var(--color-text-secondary);
      font-size: 0.9rem;
    }

    .payment-form {
      margin-top: var(--spacing-md);
    }

    .form-group {
      margin-bottom: var(--spacing-md);
    }

    .form-group label {
      display: block;
      margin-bottom: var(--spacing-xs);
      color: var(--color-text-secondary);
    }

    .form-group input {
      width: 100%;
      padding: var(--spacing-sm);
      background-color: var(--color-surface-light);
      border: 1px solid var(--color-border);
      border-radius: var(--border-radius);
      color: var(--color-text);
      transition: var(--transition);
    }

    .form-group input:focus {
      outline: none;
      border-color: var(--color-primary);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: var(--spacing-md);
    }

    .booking-summary {
      background-color: var(--color-surface);
      border-radius: var(--border-radius);
      padding: var(--spacing-md);
      border: 1px solid var(--color-border);
      position: sticky;
      top: 100px;
    }

    .summary-title {
      font-size: 1.2rem;
      margin-bottom: var(--spacing-md);
      padding-bottom: var(--spacing-sm);
      border-bottom: 1px solid var(--color-border);
    }

    .movie-summary {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: var(--spacing-md);
      padding-bottom: var(--spacing-md);
      border-bottom: 1px solid var(--color-border);
    }

    .movie-poster {
      width: 120px;
      border-radius: var(--border-radius);
      margin-bottom: var(--spacing-sm);
    }

    .movie-title {
      font-size: 1.1rem;
      font-weight: 600;
      text-align: center;
      margin-bottom: var(--spacing-xs);
    }

    .movie-meta {
      color: var(--color-text-secondary);
      font-size: 0.9rem;
      text-align: center;
    }

    .summary-details {
      margin-bottom: var(--spacing-md);
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: var(--spacing-xs);
    }

    .summary-label {
      color: var(--color-text-secondary);
    }

    .summary-value {
      font-weight: 600;
    }

    .summary-total {
      display: flex;
      justify-content: space-between;
      padding-top: var(--spacing-sm);
      border-top: 1px solid var(--color-border);
      margin-bottom: var(--spacing-md);
    }

    .total-label {
      font-size: 1.1rem;
      font-weight: 600;
    }

    .total-value {
      font-size: 1.1rem;
      color: var(--color-primary);
      font-weight: 700;
    }

    .pay-button {
      width: 100%;
      padding: var(--spacing-sm);
      background-color: var(--color-primary);
      color: var(--color-text);
      border: none;
      border-radius: var(--border-radius);
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .pay-button:hover {
      background-color: var(--color-primary-dark);
    }

    .pay-button:disabled {
      background-color: var(--color-surface-light);
      cursor: not-allowed;
    }

    .card-icons {
      display: flex;
      gap: var(--spacing-sm);
      margin-top: var(--spacing-xs);
    }

    .card-icon {
      width: 40px;
      height: 25px;
      object-fit: contain;
      filter: grayscale(1);
      opacity: 0.7;
      transition: var(--transition);
    }

    .card-icon.active {
      filter: grayscale(0);
      opacity: 1;
    }

    @media (max-width: 992px) {
      .payment-content {
        grid-template-columns: 1fr;
      }

      .booking-summary {
        position: static;
        margin-top: var(--spacing-md);
      }
    }

    @media (max-width: 768px) {
      .payment-page {
        width: 95%;
      }

      .form-row {
        grid-template-columns: 1fr;
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

  <div class="payment-page">
    <div class="payment-header">
        <h1 class="payment-title">Complete Your Payment</h1>
        <p class="payment-subtitle">Your seats are reserved for 15 minutes</p>
    </div>

    <div class="payment-content">
        <div class="payment-methods">
            @if ($errors->any())
                <div class="alert alert-danger" style="background-color: rgba(255, 68, 68, 0.1); border: 1px solid var(--color-error); color: var(--color-error); padding: var(--spacing-sm); margin-bottom: var(--spacing-md); border-radius: var(--border-radius);">
                    <ul style="list-style: none; margin: 0; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" style="background-color: rgba(255, 68, 68, 0.1); border: 1px solid var(--color-error); color: var(--color-error); padding: var(--spacing-sm); margin-bottom: var(--spacing-md); border-radius: var(--border-radius);">
                    {{ session('error') }}
                </div>
            @endif

            <form id="payment-form" method="POST" action="{{ route('payments.process') }}" autocomplete="off">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <input type="hidden" name="payment_method" id="payment_method" value="credit_card">

                <div class="payment-method active" data-method="credit_card">
                    <div class="payment-method-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="payment-method-details">
                        <div class="payment-method-title">Credit Card</div>
                        <div class="payment-method-description">
                            Pay with Visa, MasterCard, or American Express
                            <div class="card-icons">
                                <img src="{{ asset('images/visa.png') }}" alt="Visa" class="card-icon active">
                                <img src="{{ asset('images/mastercard.png') }}" alt="MasterCard" class="card-icon active">
                                <img src="{{ asset('images/amex.png') }}" alt="American Express" class="card-icon active">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="payment-method" data-method="paypal">
                    <div class="payment-method-icon">
                        <i class="fab fa-paypal"></i>
                    </div>
                    <div class="payment-method-details">
                        <div class="payment-method-title">PayPal</div>
                        <div class="payment-method-description">Pay securely using your PayPal account</div>
                    </div>
                </div>

                <div class="payment-form" id="credit-card-form">
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>

                    <div class="form-group">
                        <label for="card_name">Name on Card</label>
                        <input type="text" id="card_name" name="card_name" placeholder="John Doe">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" maxlength="5">
                        </div>

                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3">
                        </div>
                    </div>
                </div>

                <div class="payment-form" id="paypal-form" style="display: none;">
                    <p style="text-align: center; color: var(--color-text-secondary);">
                        You will be redirected to PayPal to complete your payment.
                    </p>
                </div>
            </form>
        </div>

        <div class="booking-summary">
            <h2 class="summary-title">Booking Summary</h2>

            <div class="movie-summary">
                <img src="{{ asset('storage/' . $booking->showtime->movie->poster_path) }}" alt="{{ $booking->showtime->movie->title }}" class="movie-poster">
                <h3 class="movie-title">{{ $booking->showtime->movie->title }}</h3>
                <p class="movie-meta">{{ $booking->showtime->movie->duration }} | {{ $booking->showtime->movie->rating }}</p>
            </div>

            <div class="summary-details">
                <div class="summary-item">
                    <span class="summary-label">Date:</span>
                    <span class="summary-value">{{ \Carbon\Carbon::parse($booking->showtime->date)->format('D, M j, Y') }}</span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Time:</span>
                    <span class="summary-value">{{ \Carbon\Carbon::parse($booking->showtime->time)->format('g:i A') }}</span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Seats:</span>
                    <span class="summary-value">{{ implode(', ', $booking->seats->map(function($seat) {
                        return $seat->row . $seat->number;
                    })->toArray()) }}</span>
                </div>

                <div class="summary-item">
                    <span class="summary-label">Tickets:</span>
                    <span class="summary-value">{{ $booking->seats->count() }} × $12.00</span>
                </div>
            </div>

            <div class="summary-total">
                <span class="total-label">Total:</span>
                <span class="total-value">${{ number_format($booking->total_price, 2) }}</span>
            </div>

            <button type="submit" form="payment-form" class="pay-button">Pay Now</button>
        </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  // Mobile menu toggle
  document.querySelector('.menu-btn').addEventListener('click', () => {
    document.querySelector('.navbar').classList.toggle('active');
  });

  document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('.payment-method');
    const paymentMethodInput = document.getElementById('payment_method');
    const creditCardForm = document.getElementById('credit-card-form');
    const paypalForm = document.getElementById('paypal-form');
    const paymentForm = document.getElementById('payment-form');
    const submitButton = document.querySelector('.pay-button');

    // Form submission handling
    submitButton.addEventListener('click', function(e) {
        e.preventDefault();

        // Basic validation
        if (paymentMethodInput.value === 'credit_card') {
            const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
            const cardName = document.getElementById('card_name').value.trim();
            const expiryDate = document.getElementById('expiry_date').value.trim();
            const cvv = document.getElementById('cvv').value.trim();

            if (!cardNumber || cardNumber.length < 16) {
                alert('Please enter a valid card number');
                return;
            }
            if (!cardName) {
                alert('Please enter the name on card');
                return;
            }
            if (!expiryDate || !expiryDate.match(/^\d{2}\/\d{2}$/)) {
                alert('Please enter a valid expiry date (MM/YY)');
                return;
            }
            if (!cvv || !cvv.match(/^\d{3}$/)) {
                alert('Please enter a valid CVV');
                return;
            }
        }

        // If validation passes, submit the form
        paymentForm.submit();
    });

    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            // Remove active class from all methods
            paymentMethods.forEach(m => m.classList.remove('active'));

            // Add active class to clicked method
            this.classList.add('active');

            // Update hidden input
            const methodType = this.getAttribute('data-method');
            paymentMethodInput.value = methodType;

            // Show/hide appropriate form
            if (methodType === 'credit_card') {
                creditCardForm.style.display = 'block';
                paypalForm.style.display = 'none';
            } else {
                creditCardForm.style.display = 'none';
                paypalForm.style.display = 'block';
            }
        });
    });

    // Card number formatting
    const cardNumber = document.getElementById('card_number');
    cardNumber.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(.{4})/g, '$1 ').trim();
        e.target.value = value;
    });

    // Expiry date formatting
    const expiryDate = document.getElementById('expiry_date');
    expiryDate.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2);
        }
        e.target.value = value;
    });

    // CVV numbers only
    const cvv = document.getElementById('cvv');
    cvv.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });
  });
</script>
@endsection
