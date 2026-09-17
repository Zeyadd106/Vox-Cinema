@extends('layouts.app')

@section('title', 'My Payments')

@section('content')
<div style="padding-top:120px;max-width:1000px;margin:0 auto;width:90%;min-height:80vh;color:#fff;">
    <h1 style="color:#E31837;text-align:center;margin-bottom:2rem;">My Payments</h1>
    @forelse($payments as $payment)
    <div style="background:#1a1a1a;border:1px solid #333;border-radius:8px;padding:1rem;margin-bottom:1rem;">
        <strong>{{ $payment->transaction_id }}</strong> — ${{ number_format($payment->amount, 2) }}
        <span style="color:#ccc;">| {{ $payment->payment_method }} | {{ $payment->status }}</span>
        <span style="color:#ccc;">| Booking: {{ optional($payment->booking)->booking_reference }}</span>
    </div>
    @empty
    <p style="text-align:center;">No payments yet.</p>
    @endforelse
</div>
@endsection
