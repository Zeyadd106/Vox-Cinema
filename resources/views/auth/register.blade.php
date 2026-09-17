@extends('layouts.app')

@section('title', 'Register')

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
      --color-error: #ff4444;
      --color-success: #00C851;

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
      background: var(--color-background);
      color: var(--color-text);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Header styles from login page */
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

    /* Footer */
    footer {
      text-align: center;
      padding: var(--spacing-md);
      color: var(--color-text-secondary);
      font-size: 0.875rem;
      background: var(--color-surface);
      border-top: 1px solid var(--color-border);
      margin-top: auto;
    }

    /* Register Form */
    .auth-section {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: var(--spacing-lg) var(--spacing-md);
      margin-top: 80px;
      background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                  url('{{ asset('images/cinema-bg.jpg') }}') center/cover;
    }

    .auth-container {
      width: 100%;
      max-width: 500px;
      background-color: var(--color-surface);
      padding: var(--spacing-lg);
      border-radius: var(--border-radius);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
      border: 1px solid var(--color-border);
      animation: slideUp 0.5s ease;
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

    .auth-container h2 {
      text-align: center;
      color: var(--color-primary);
      margin-bottom: var(--spacing-lg);
      font-size: 2rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .form-group {
      margin-bottom: var(--spacing-md);
      position: relative;
    }

    .form-group label {
      font-weight: 500;
      margin-bottom: var(--spacing-xs);
      display: block;
      color: var(--color-text);
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .form-group .input-wrapper {
      position: relative;
    }

    .form-group .input-icon {
      position: absolute;
      left: var(--spacing-sm);
      top: 50%;
      transform: translateY(-50%);
      color: var(--color-text-secondary);
      font-size: 1.2rem;
    }

    .form-group input {
      width: 100%;
      padding: var(--spacing-sm) var(--spacing-sm) var(--spacing-sm) 3rem;
      border: 1px solid var(--color-border);
      border-radius: var(--border-radius);
      transition: var(--transition);
      font-size: 1rem;
      background: var(--color-surface-light);
      color: var(--color-text);
    }

    .form-group input:focus {
      border-color: var(--color-primary);
      outline: none;
      box-shadow: 0 0 0 2px rgba(227, 24, 55, 0.2);
    }

    .form-group .error-message {
      color: var(--color-error);
      font-size: 0.8rem;
      margin-top: var(--spacing-xs);
      display: none;
    }

    .form-group.error input {
      border-color: var(--color-error);
    }

    .form-group.error .error-message {
      display: block;
    }

    .password-toggle {
      position: absolute;
      right: var(--spacing-sm);
      top: 50%;
      transform: translateY(-50%);
      color: var(--color-text-secondary);
      cursor: pointer;
      font-size: 1.1rem;
    }

    .auth-container .btn {
      width: 100%;
      padding: var(--spacing-sm);
      background-color: var(--color-primary);
      color: var(--color-text);
      font-size: 1rem;
      border: 2px solid var(--color-primary);
      border-radius: var(--border-radius);
      cursor: pointer;
      transition: var(--transition);
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 1px;
      position: relative;
      overflow: hidden;
    }

    .auth-container .btn:hover {
      background-color: transparent;
      color: var(--color-primary);
    }

    .auth-container .btn::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    .auth-container .btn:active::after {
      width: 300px;
      height: 300px;
    }

    .auth-container .switch {
      margin-top: var(--spacing-md);
      text-align: center;
      color: var(--color-text);
      font-size: 0.9rem;
    }

    .auth-container .switch a {
      color: var(--color-primary);
      text-decoration: none;
      font-weight: 600;
      transition: var(--transition);
    }

    .auth-container .switch a:hover {
      color: var(--color-primary-light);
    }

    .social-login {
      margin-top: var(--spacing-lg);
      text-align: center;
    }

    .social-login p {
      color: var(--color-text-secondary);
      margin-bottom: var(--spacing-sm);
      font-size: 0.9rem;
      position: relative;
    }

    .social-login p::before,
    .social-login p::after {
      content: '';
      position: absolute;
      top: 50%;
      width: 30%;
      height: 1px;
      background: var(--color-border);
    }

    .social-login p::before {
      left: 0;
    }

    .social-login p::after {
      right: 0;
    }

    .social-buttons {
      display: flex;
      gap: var(--spacing-sm);
      justify-content: center;
    }

    .social-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--color-border);
      background: var(--color-surface-light);
      color: var(--color-text);
      font-size: 1.2rem;
      cursor: pointer;
      transition: var(--transition);
    }

    .social-btn:hover {
      border-color: var(--color-primary);
      color: var(--color-primary);
      transform: translateY(-2px);
    }

    /* Terms and Conditions */
    .terms {
      margin-bottom: var(--spacing-md);
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
    }

    .terms input[type="checkbox"] {
      width: 16px;
      height: 16px;
      accent-color: var(--color-primary);
    }

    .terms label {
      font-size: 0.9rem;
      color: var(--color-text-secondary);
    }

    .terms a {
      color: var(--color-primary);
      text-decoration: none;
      transition: var(--transition);
    }

    .terms a:hover {
      color: var(--color-primary-light);
    }

    /* Success Animation */
    .success-checkmark {
      display: none;
      color: var(--color-success);
      font-size: 3rem;
      text-align: center;
      margin-bottom: var(--spacing-md);
      animation: scaleIn 0.5s ease;
    }

    @keyframes scaleIn {
      from {
        transform: scale(0);
      }
      to {
        transform: scale(1);
      }
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

      .auth-container {
        margin: 0 var(--spacing-sm);
        padding: var(--spacing-md);
      }

      .social-login p::before,
      .social-login p::after {
        width: 25%;
      }
    }

    @media (max-width: 480px) {
      .header {
        padding: var(--spacing-sm) 4%;
      }

      .header .logo {
        font-size: 1.5rem;
      }

      .auth-container {
        padding: var(--spacing-md);
      }

      .auth-container h2 {
        font-size: 1.5rem;
      }

      .form-group input {
        font-size: 0.9rem;
      }

      .social-login p::before,
      .social-login p::after {
        width: 20%;
      }
    }

    /* Accessibility */
    @media (prefers-reduced-motion: reduce) {
      * {
        animation: none !important;
        transition: none !important;
      }
    }

    @media (prefers-contrast: high) {
      :root {
        --color-primary: #ff0000;
      }

      .auth-container {
        border: 2px solid var(--color-text);
      }

      input {
        border: 2px solid var(--color-text);
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

  <!-- Register Form -->
  <section class="auth-section">
    <div class="auth-container">
      <div class="success-checkmark">
        <i class="fas fa-check-circle"></i>
      </div>

      <h2>Create Account</h2>

      <form id="registerForm" method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-group @error('name') error @enderror">
          <label for="name">Full Name</label>
          <div class="input-wrapper">
            <i class="fas fa-user input-icon"></i>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required />
            <span class="error-message">@error('name') {{ $message }} @else Please enter your full name @enderror</span>
          </div>
        </div>

        <div class="form-group @error('email') error @enderror">
          <label for="email">Email Address</label>
          <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required />
            <span class="error-message">@error('email') {{ $message }} @else Please enter a valid email address @enderror</span>
          </div>
        </div>

        <div class="form-group @error('password') error @enderror">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" id="password" name="password" required />
            <i class="fas fa-eye password-toggle"></i>
            <span class="error-message">@error('password') {{ $message }} @else Password must be at least 8 characters @enderror</span>
          </div>
        </div>

        <div class="form-group @error('password_confirmation') error @enderror">
          <label for="password_confirmation">Confirm Password</label>
          <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" id="password_confirmation" name="password_confirmation" required />
            <i class="fas fa-eye password-toggle"></i>
            <span class="error-message">Passwords do not match</span>
          </div>
        </div>

        <div class="terms">
          <input type="checkbox" id="terms" name="terms" required />
          <label for="terms">
            I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
          </label>
        </div>

        <button type="submit" class="btn">Register</button>
      </form>

      <p class="switch">
        Already have an account? <a href="{{ route('login') }}">Login</a>
      </p>

      <div class="social-login">
        <p>Or register with</p>
        <div class="social-buttons">
          <button class="social-btn">
            <i class="fab fa-google"></i>
          </button>
          <button class="social-btn">
            <i class="fab fa-facebook-f"></i>
          </button>
          <button class="social-btn">
            <i class="fab fa-twitter"></i>
          </button>
        </div>
      </div>
    </div>
  </section>
@endsection

@section('scripts')
<script>
  // Mobile menu toggle
  document.querySelector('.menu-btn').addEventListener('click', () => {
    document.querySelector('.navbar').classList.toggle('active');
  });

  // Password visibility toggle
  document.querySelectorAll('.password-toggle').forEach(toggle => {
    toggle.addEventListener('click', (e) => {
      const input = e.target.previousElementSibling;
      const type = input.type === 'password' ? 'text' : 'password';
      input.type = type;
      e.target.classList.toggle('fa-eye');
      e.target.classList.toggle('fa-eye-slash');
    });
  });

  // Password match validation
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('password_confirmation');
  const form = document.getElementById('registerForm');

  form.addEventListener('submit', function(e) {
    if (password.value !== confirmPassword.value) {
      e.preventDefault();
      confirmPassword.closest('.form-group').classList.add('error');
    }
  });

  // Real-time validation
  confirmPassword.addEventListener('input', function() {
    if (password.value === confirmPassword.value) {
      confirmPassword.closest('.form-group').classList.remove('error');
    } else {
      confirmPassword.closest('.form-group').classList.add('error');
    }
  });

  // General input validation
  document.querySelectorAll('input:not(#password_confirmation)').forEach(input => {
    input.addEventListener('input', () => {
      const group = input.closest('.form-group');
      if (input.checkValidity()) {
        group.classList.remove('error');
      }
    });
  });
</script>
@endsection
