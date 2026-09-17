<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\PaymentConfirmation;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $payments = Payment::whereHas('booking', function ($query) {
            $query->where('user_id', Auth::id());
        })->with(['booking.showtime.movie'])->latest()->get();

        return view('payments.index', compact('payments'));
    }

    public function create(Booking $booking)
    {
        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== Auth::id()) {
            return redirect()->route('bookings.index')
                ->with('error', 'Unauthorized access to booking');
        }

        // Check if payment is already completed
        if ($booking->payment_status === 'paid') {
            return redirect()->route('bookings.show', $booking->id)
                ->with('info', 'Payment has already been completed for this booking');
        }

        return view('payments.create', compact('booking'));
    }

    public function process(Request $request)
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'payment_method' => 'required|in:credit_card,paypal',
                'card_number' => 'required_if:payment_method,credit_card|string|min:16',
                'card_name' => 'required_if:payment_method,credit_card|string|min:3',
                'expiry_date' => 'required_if:payment_method,credit_card|string|regex:/^\d{2}\/\d{2}$/',
                'cvv' => 'required_if:payment_method,credit_card|string|size:3',
            ]);

            $booking = Booking::findOrFail($request->booking_id);
            
            // Check if the booking belongs to the authenticated user
            if ($booking->user_id !== Auth::id()) {
                throw new \Exception('Unauthorized access to booking');
            }

            // Process payment through payment service
            $result = $this->paymentService->processPayment($booking, $request->all());

            // Send payment confirmation email
            Auth::user()->notify(new PaymentConfirmation($result['payment']));

            return redirect()->route('bookings.confirmation', $booking)
                           ->with('success', 'Payment processed successfully!');

        } catch (\Exception $e) {
            \Log::error('Payment processing failed: ' . $e->getMessage());
            
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function confirmation(Booking $booking)
    {
        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== Auth::id()) {
            return redirect()->route('bookings.index')
                ->with('error', 'Unauthorized access to booking');
        }

        // Check if payment is completed
        if ($booking->payment_status !== 'paid') {
            return redirect()->route('bookings.show', $booking)
                ->with('error', 'Payment has not been completed for this booking');
        }

        return view('bookings.confirmation', compact('booking'));
    }
}
