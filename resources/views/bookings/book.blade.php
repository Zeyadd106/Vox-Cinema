@extends('layouts.app')

@section('title', 'Book Tickets - ' . $movie->title)

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

    /* Booking Styles */
    .booking-page {
      padding-top: 120px;
      padding-bottom: var(--spacing-lg);
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
    }

    .booking-header {
      margin-bottom: var(--spacing-lg);
      text-align: center;
    }

    .booking-title {
      font-size: 2rem;
      color: var(--color-primary);
      margin-bottom: var(--spacing-sm);
    }

    .booking-subtitle {
      font-size: 1.2rem;
      color: var(--color-text-secondary);
    }

    .booking-steps {
      display: flex;
      justify-content: center;
      margin-bottom: var(--spacing-lg);
      position: relative;
    }

    .booking-steps::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 20%;
      right: 20%;
      height: 2px;
      background-color: var(--color-border);
      z-index: 0;
    }

    .booking-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 150px;
      position: relative;
      z-index: 1;
    }

    .step-number {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--color-surface-light);
      color: var(--color-text);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      margin-bottom: var(--spacing-xs);
      border: 2px solid var(--color-border);
      transition: var(--transition);
    }

    .step-label {
      font-size: 0.9rem;
      color: var(--color-text-secondary);
      text-align: center;
      transition: var(--transition);
    }

    .booking-step.active .step-number {
      background-color: var(--color-primary);
      border-color: var(--color-primary);
    }

    .booking-step.active .step-label {
      color: var(--color-primary);
    }

    .booking-step.completed .step-number {
      background-color: var(--color-available);
      border-color: var(--color-available);
    }

    .booking-content {
      display: grid;
      grid-template-columns: 1fr 300px;
      gap: var(--spacing-lg);
    }

    /* Step 1: Showtime Selection */
    .step-content {
      display: none;
    }

    .step-content.active {
      display: block;
      animation: fadeIn 0.3s ease;
    }

    .showtime-selection {
      background-color: var(--color-surface);
      border-radius: var(--border-radius);
      padding: var(--spacing-md);
      border: 1px solid var(--color-border);
    }

    .selection-title {
      font-size: 1.2rem;
      margin-bottom: var(--spacing-md);
      color: var(--color-primary);
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
    }

    .date-selector {
      display: flex;
      gap: var(--spacing-xs);
      margin-bottom: var(--spacing-md);
      overflow-x: auto;
      padding-bottom: var(--spacing-sm);
    }

    .date-selector::-webkit-scrollbar {
      height: 8px;
    }

    .date-selector::-webkit-scrollbar-track {
      background: var(--color-surface-light);
      border-radius: 10px;
    }

    .date-selector::-webkit-scrollbar-thumb {
      background: var(--color-primary);
      border-radius: 10px;
    }

    .date-item {
      min-width: 100px;
      padding: var(--spacing-sm);
      background-color: var(--color-surface-light);
      border-radius: var(--border-radius);
      text-align: center;
      cursor: pointer;
      transition: var(--transition);
      border: 1px solid var(--color-border);
    }

    .date-item.active {
      background-color: var(--color-primary);
      border-color: var(--color-primary);
    }

    .date-item:hover:not(.active) {
      background-color: rgba(227, 24, 55, 0.1);
      border-color: var(--color-primary);
    }

    .date-day {
      font-size: 1.2rem;
      font-weight: 600;
    }

    .date-date {
      font-size: 0.8rem;
      color: var(--color-text-secondary);
    }

    .date-item.active .date-date {
      color: var(--color-text);
    }

    .time-selector {
      display: none;
    }

    .time-selector.active {
      display: block;
      animation: fadeIn 0.3s ease;
    }

    .time-slots {
      display: flex;
      flex-wrap: wrap;
      gap: var(--spacing-sm);
    }

    .time-slot {
      padding: var(--spacing-sm) var(--spacing-md);
      background-color: var(--color-surface-light);
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: var(--transition);
      border: 1px solid var(--color-border);
    }

    .time-slot.active {
      background-color: var(--color-primary);
      border-color: var(--color-primary);
    }

    .time-slot:hover:not(.active) {
      background-color: rgba(227, 24, 55, 0.1);
      border-color: var(--color-primary);
    }

    /* Step 2: Seat Selection */
    .seat-selection {
      background-color: var(--color-surface);
      border-radius: var(--border-radius);
      padding: var(--spacing-md);
      border: 1px solid var(--color-border);
    }

    .screen-container {
      margin-bottom: var(--spacing-lg);
      position: relative;
      padding-top: var(--spacing-md);
    }

    .screen {
      height: 60px;
      background: linear-gradient(to bottom, var(--color-primary), transparent);
      border-top-left-radius: 50%;
      border-top-right-radius: 50%;
      position: relative;
      margin-bottom: var(--spacing-lg);
    }

    .screen::before {
      content: 'SCREEN';
      position: absolute;
      top: 10px;
      left: 50%;
      transform: translateX(-50%);
      color: var(--color-text);
      font-weight: 600;
      font-size: 0.8rem;
      letter-spacing: 2px;
    }

    .seating-area {
      display: flex;
      flex-direction: column;
      gap: var(--spacing-sm);
      align-items: center;
    }

    .seat-row {
      display: flex;
      gap: var(--spacing-xs);
      align-items: center;
    }

    .row-label {
      width: 30px;
      text-align: center;
      font-weight: 600;
      color: var(--color-text-secondary);
    }

    .seats {
      display: flex;
      gap: var(--spacing-xs);
    }

    .seat {
      width: 30px;
      height: 30px;
      border-radius: 5px 5px 0 0;
      background-color: var(--color-surface-light);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.8rem;
      color: var(--color-text);
      cursor: pointer;
      transition: var(--transition);
      border: 1px solid var(--color-border);
      position: relative;
    }

    .seat.available:hover {
      background-color: rgba(0, 200, 81, 0.2);
      transform: scale(1.1);
    }

    .seat.available {
      background-color: var(--color-surface-light);
      border-color: var(--color-available);
    }

    .seat.unavailable {
      background-color: var(--color-surface-light);
      border-color: var(--color-unavailable);
      cursor: not-allowed;
      color: var(--color-text-secondary);
    }

    .seat.selected {
      background-color: var(--color-selected);
      border-color: var(--color-selected);
      color: var(--color-background);
    }

    .seat-gap {
      width: var(--spacing-sm);
    }

    .seat-legend {
      display: flex;
      justify-content: center;
      gap: var(--spacing-md);
      margin-top: var(--spacing-lg);
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
      font-size: 0.9rem;
      color: var(--color-text-secondary);
    }

    .legend-color {
      width: 20px;
      height: 20px;
      border-radius: 5px 5px 0 0;
    }

    .legend-available {
      background-color: var(--color-surface-light);
      border: 1px solid var(--color-available);
    }

    .legend-selected {
      background-color: var(--color-selected);
      border: 1px solid var(--color-selected);
    }

    .legend-unavailable {
      background-color: var(--color-surface-light);
      border: 1px solid var(--color-unavailable);
    }

    /* Booking Summary */
    .booking-summary {
      background-color: var(--color-surface);
      border-radius: var(--border-radius);
      padding: var(--spacing-md);
      position: sticky;
      top: 100px;
      border: 1px solid var(--color-border);
    }

    .summary-title {
      font-size: 1.2rem;
      margin-bottom: var(--spacing-md);
      color: var(--color-text);
      text-align: center;
    }

    .movie-summary {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: var(--spacing-md);
      padding-bottom: var(--spacing-md);
      border-bottom: 1px solid var(--color-border);
    }

    .movie-poster-small {
      width: 120px;
      border-radius: var(--border-radius);
      margin-bottom: var(--spacing-sm);
    }

    .movie-name {
      font-weight: 600;
      text-align: center;
      margin-bottom: 4px;
    }

    .movie-meta-small {
      font-size: 0.9rem;
      color: var(--color-text-secondary);
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
      color: var(--color-text);
      font-weight: 600;
    }

    .total-value {
      font-size: 1.1rem;
      color: var(--color-primary);
      font-weight: 700;
    }

    .proceed-btn {
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

    .proceed-btn:hover {
      background-color: var(--color-primary-dark);
    }

    .proceed-btn:disabled {
      background-color: var(--color-surface-light);
      color: var(--color-text-secondary);
      cursor: not-allowed;
    }

    .back-btn {
      width: 100%;
      padding: var(--spacing-sm);
      background-color: transparent;
      color: var(--color-text-secondary);
      border: 1px solid var(--color-border);
      border-radius: var(--border-radius);
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
      margin-top: var(--spacing-sm);
      display: none;
    }

    .back-btn.visible {
      display: block;
    }

    .back-btn:hover {
      background-color: var(--color-surface-light);
      color: var(--color-text);
    }

    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    /* Responsive Design */
    @media (max-width: 992px) {
      .booking-content {
        grid-template-columns: 1fr;
      }

      .booking-summary {
        position: static;
        margin-top: var(--spacing-md);
      }
    }

    @media (max-width: 768px) {
      .booking-page {
        width: 95%;
      }

      .booking-steps {
        flex-wrap: wrap;
        justify-content: center;
      }

      .booking-steps::after {
        display: none;
      }

      .date-selector {
        justify-content: flex-start;
      }

      .seat {
        width: 25px;
        height: 25px;
        font-size: 0.7rem;
      }

      .row-label {
        width: 20px;
      }

      .seat-legend {
        flex-wrap: wrap;
      }
    }

    @media (max-width: 480px) {
      .booking-title {
        font-size: 1.5rem;
      }

      .booking-subtitle {
        font-size: 1rem;
      }

      .step-number {
        width: 30px;
        height: 30px;
      }

      .step-label {
        font-size: 0.8rem;
      }

      .time-slots {
        justify-content: center;
      }

      .seat {
        width: 20px;
        height: 20px;
        font-size: 0.6rem;
      }

      .form-row {
        flex-direction: column;
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

  <div class="booking-page">
    <div class="booking-header">
      <h1 class="booking-title">Book Tickets</h1>
      <p class="booking-subtitle">{{ $movie->title }}</p>
    </div>

    <div class="booking-steps">
      <div class="booking-step active" data-step="1">
        <div class="step-number">1</div>
        <div class="step-label">Select Showtime</div>
      </div>

      <div class="booking-step" data-step="2">
        <div class="step-number">2</div>
        <div class="step-label">Select Seats</div>
      </div>
    </div>

    <div class="booking-content">
      <div class="booking-main">
        <!-- Step 1: Showtime Selection -->
        <div class="step-content active" id="step-1">
          <div class="showtime-selection">
            <h2 class="selection-title"><i class="fas fa-calendar-alt"></i> Select Date & Time</h2>

            <div class="date-selector">
              @foreach($showtimes as $date => $dayShowtimes)
                @php
                  $carbonDate = \Carbon\Carbon::parse($date);
                @endphp
                <div class="date-item" data-date="{{ $date }}">
                  <div class="date-day">{{ $carbonDate->format('D') }}</div>
                  <div class="date-date">{{ $carbonDate->format('M j') }}</div>
                </div>
              @endforeach
            </div>

            @foreach($showtimes as $date => $dayShowtimes)
              <div class="time-selector" id="time-{{ $date }}">
                <div class="time-slots">
                  @foreach($dayShowtimes as $showtime)
                    <div class="time-slot" data-time="{{ $showtime->time }}" data-showtime-id="{{ $showtime->id }}">
                      {{ \Carbon\Carbon::parse($showtime->time)->format('g:i A') }}
                    </div>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Step 2: Seat Selection -->
        <div class="step-content" id="step-2">
          <div class="seat-selection">
            <h2 class="selection-title"><i class="fas fa-chair"></i> Select Your Seats</h2>

            <div class="screen-container">
              <div class="screen"></div>
            </div>

            <div class="seating-area">
              @php
                $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                $seatsPerRow = 10;
              @endphp

              @foreach($rows as $row)
                <div class="seat-row">
                  <div class="row-label">{{ $row }}</div>
                  <div class="seats">
                    @for($i = 1; $i <= $seatsPerRow; $i++)
                      @if($i == 4)
                        <div class="seat-gap"></div>
                      @endif

                      <div class="seat available" data-row="{{ $row }}" data-number="{{ $i }}">
                        {{ $i }}
                      </div>

                      @if($i == 7)
                        <div class="seat-gap"></div>
                      @endif
                    @endfor
                  </div>
                </div>
              @endforeach
            </div>

            <div class="seat-legend">
              <div class="legend-item">
                <div class="legend-color legend-available"></div>
                <span>Available</span>
              </div>

              <div class="legend-item">
                <div class="legend-color legend-selected"></div>
                <span>Selected</span>
              </div>

              <div class="legend-item">
                <div class="legend-color legend-unavailable"></div>
                <span>Unavailable</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="booking-summary">
        <h2 class="summary-title">Booking Summary</h2>

        <div class="movie-summary">
          <img src="{{ asset('storage/' . $movie->poster_path) }}" alt="{{ $movie->title }}" class="movie-poster-small">
          <h3 class="movie-name">{{ $movie->title }}</h3>
          <p class="movie-meta-small">{{ $movie->duration }} | {{ $movie->rating }}</p>
        </div>

        <div class="summary-details">
          <div class="summary-item">
            <span class="summary-label">Date:</span>
            <span class="summary-value" id="summary-date">Select a date</span>
          </div>

          <div class="summary-item">
            <span class="summary-label">Time:</span>
            <span class="summary-value" id="summary-time">Select a time</span>
          </div>

          <div class="summary-item">
            <span class="summary-label">Seats:</span>
            <span class="summary-value" id="summary-seats">Select your seats</span>
          </div>

          <div class="summary-item">
            <span class="summary-label">Ticket Price:</span>
            <span class="summary-value">$12.00 per ticket</span>
          </div>
        </div>

        <div class="summary-total">
          <span class="total-label">Total:</span>
          <span class="total-value" id="summary-total">$0.00</span>
        </div>

        <form id="booking-form" method="POST" action="{{ route('bookings.store') }}">
          @csrf
          <input type="hidden" id="showtime_id" name="showtime_id">
          <input type="hidden" id="seat_ids" name="seat_ids">
          <input type="hidden" id="total_price" name="total_price">

          <button type="submit" id="proceed-btn" class="proceed-btn" disabled>Proceed to Payment</button>
          <button type="button" id="back-btn" class="back-btn">Go Back</button>
        </form>
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
    // Variables
    let currentStep = 1;
    let selectedDate = '';
    let selectedShowtime = '';
    let selectedShowtimeId = '';
    let selectedSeats = [];
    let selectedSeatIds = [];
    let totalPrice = 0;
    const ticketPrice = 12.00;

    // DOM elements
    const steps = document.querySelectorAll('.booking-step');
    const stepContents = document.querySelectorAll('.step-content');
    const proceedBtn = document.getElementById('proceed-btn');
    const backBtn = document.getElementById('back-btn');
    const dateItems = document.querySelectorAll('.date-item');
    const timeSelectors = document.querySelectorAll('.time-selector');
    const timeSlots = document.querySelectorAll('.time-slot');
    const seats = document.querySelectorAll('.seat');
    const bookingForm = document.getElementById('booking-form');

    // Summary elements
    const summaryDate = document.getElementById('summary-date');
    const summaryTime = document.getElementById('summary-time');
    const summarySeats = document.getElementById('summary-seats');
    const summaryTotal = document.getElementById('summary-total');

    // Form elements
    const showtimeIdInput = document.getElementById('showtime_id');
    const seatIdsInput = document.getElementById('seat_ids');
    const totalPriceInput = document.getElementById('total_price');

    // Helper functions
    function updateStep(step) {
      currentStep = step;

      // Update step indicators
      steps.forEach(item => {
        const stepNum = parseInt(item.getAttribute('data-step'));
        item.classList.remove('active', 'completed');

        if (stepNum === currentStep) {
          item.classList.add('active');
        } else if (stepNum < currentStep) {
          item.classList.add('completed');
        }
      });

      // Show current step content
      stepContents.forEach(content => {
        content.classList.remove('active');
      });
      document.getElementById(`step-${currentStep}`).classList.add('active');

      // Update button text
      if (currentStep === 2) {
        proceedBtn.textContent = 'Proceed to Payment';
      } else {
        proceedBtn.textContent = 'Next Step';
      }

      // Show/hide back button
      if (currentStep > 1) {
        backBtn.classList.add('visible');
      } else {
        backBtn.classList.remove('visible');
      }

      // Check if proceed button should be enabled
      checkProceedButton();
    }

    function checkProceedButton() {
      switch (currentStep) {
        case 1:
          proceedBtn.disabled = !selectedShowtimeId;
          break;
        case 2:
          proceedBtn.disabled = selectedSeats.length === 0;
          break;
      }
    }

    function updateSummary() {
      summaryDate.textContent = selectedDate || 'Select a date';
      summaryTime.textContent = selectedShowtime || 'Select a time';

      if (selectedSeats.length > 0) {
        summarySeats.textContent = selectedSeats.join(', ');
      } else {
        summarySeats.textContent = 'Select your seats';
      }

      totalPrice = selectedSeats.length * ticketPrice;
      summaryTotal.textContent = `$${totalPrice.toFixed(2)}`;

      // Update form inputs
      showtimeIdInput.value = selectedShowtimeId;
      seatIdsInput.value = JSON.stringify(selectedSeatIds);
      totalPriceInput.value = totalPrice;
    }

    // Date selection
    dateItems.forEach(item => {
      item.addEventListener('click', function() {
        dateItems.forEach(date => date.classList.remove('active'));
        this.classList.add('active');

        const date = this.getAttribute('data-date');
        selectedDate = this.querySelector('.date-day').textContent + ', ' + this.querySelector('.date-date').textContent;

        // Show corresponding time selector
        timeSelectors.forEach(selector => selector.classList.remove('active'));
        document.getElementById(`time-${date}`).classList.add('active');

        // Reset time selection
        timeSlots.forEach(slot => slot.classList.remove('active'));
        selectedShowtime = '';
        selectedShowtimeId = '';

        updateSummary();
        checkProceedButton();
      });
    });

    // Time selection
    timeSlots.forEach(slot => {
      slot.addEventListener('click', function() {
        timeSlots.forEach(time => time.classList.remove('active'));
        this.classList.add('active');

        selectedShowtime = this.getAttribute('data-time');
        selectedShowtimeId = this.getAttribute('data-showtime-id');

        updateSummary();
        checkProceedButton();
      });
    });

    // Seat selection
    seats.forEach(seat => {
      seat.addEventListener('click', function() {
        if (this.classList.contains('unavailable')) {
          return;
        }

        const row = this.getAttribute('data-row');
        const number = this.getAttribute('data-number');
        const seatLabel = row + number;
        const seatId = row.charCodeAt(0) - 64 + (number - 1) * 7; // Simplified ID generation

        if (this.classList.contains('selected')) {
          // Deselect seat
          this.classList.remove('selected');
          this.classList.add('available');

          const index = selectedSeats.indexOf(seatLabel);
          if (index !== -1) {
            selectedSeats.splice(index, 1);
            selectedSeatIds.splice(index, 1);
          }
        } else {
          // Select seat
          this.classList.add('selected');
          this.classList.remove('available');
          selectedSeats.push(seatLabel);
          selectedSeatIds.push(seatId);
        }

        updateSummary();
        checkProceedButton();
      });
    });

    // Next step button
    proceedBtn.addEventListener('click', function() {
      if (currentStep < 2) {
        updateStep(currentStep + 1);
      } else {
        // Submit the form to create booking and redirect to payment
        bookingForm.submit();
      }
    });

    // Back button
    backBtn.addEventListener('click', function() {
      if (currentStep > 1) {
        updateStep(currentStep - 1);
      }
    });

    // Load available seats from API
    function loadAvailableSeats() {
      if (!selectedShowtimeId) return;

      // In a real application, you would fetch this from the server
      // For demonstration, we'll randomly mark some seats as unavailable
      seats.forEach(seat => {
        seat.classList.remove('unavailable', 'selected');
        seat.classList.add('available');

        if (Math.random() < 0.3) {
          seat.classList.remove('available');
          seat.classList.add('unavailable');
        }
      });
    }

    // Initialize
    updateStep(1);

    // Set initial date
    if (dateItems.length > 0) {
      dateItems[0].click();
    }

    // Pre-select showtime if provided in URL
    const urlParams = new URLSearchParams(window.location.search);
    const showtimeParam = urlParams.get('showtime');

    if (showtimeParam) {
      const showtimeSlot = document.querySelector(`.time-slot[data-showtime-id="${showtimeParam}"]`);
      if (showtimeSlot) {
        // Find parent time selector and activate it
        const timeSelector = showtimeSlot.closest('.time-selector');
        if (timeSelector) {
          const dateId = timeSelector.id.replace('time-', '');
          const dateItem = document.querySelector(`.date-item[data-date="${dateId}"]`);
          if (dateItem) {
            dateItem.click();
          }
        }

        // Activate the showtime
        showtimeSlot.click();
      }
    }
  });
</script>
@endsection
