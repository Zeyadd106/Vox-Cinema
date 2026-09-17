<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Process a payment
     *
     * @param Booking $booking
     * @param array $paymentData
     * @return array
     */
    public function processPayment(Booking $booking, array $paymentData): array
    {
        try {
            DB::beginTransaction();

            // Check if payment already exists
            if ($booking->payment) {
                throw new Exception('Payment already processed for this booking');
            }

            // Validate booking status
            if ($booking->payment_status === 'paid') {
                throw new Exception('This booking has already been paid');
            }

            // Process payment based on method
            $result = match($paymentData['payment_method']) {
                'credit_card' => $this->processCreditCardPayment($booking, $paymentData),
                'paypal' => $this->processPaypalPayment($booking, $paymentData),
                default => throw new Exception('Invalid payment method'),
            };

            DB::commit();
            return $result;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            throw new Exception('Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Process a credit card payment
     *
     * @param Booking $booking
     * @param array $paymentData
     * @return array
     */
    protected function processCreditCardPayment(Booking $booking, array $paymentData): array
    {
        try {
            // Validate credit card data
            if (!isset($paymentData['card_number'], $paymentData['card_name'], $paymentData['expiry_date'], $paymentData['cvv'])) {
                throw new Exception('Missing credit card information');
            }

            // Clean up card number and get last four digits
            $cardNumber = preg_replace('/\D/', '', $paymentData['card_number']);
            $cardLastFour = substr($cardNumber, -4);

            // Create payment record
            $payment = new Payment([
                'booking_id' => $booking->id,
                'payment_method' => 'credit_card',
                'amount' => $booking->total_price,
                'status' => 'completed',
                'transaction_id' => 'CC_' . uniqid(),
                'card_last_four' => $cardLastFour
            ]);

            $payment->save();

            // Update booking status
            $booking->update([
                'payment_status' => 'paid',
                'payment_method' => 'credit_card',
                'paid_at' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Payment processed successfully',
                'payment' => $payment,
                'booking' => $booking
            ];
        } catch (Exception $e) {
            Log::error('Credit card payment error: ' . $e->getMessage());
            throw new Exception('Credit card payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Process a PayPal payment
     *
     * @param Booking $booking
     * @param array $paymentData
     * @return array
     */
    protected function processPaypalPayment(Booking $booking, array $paymentData): array
    {
        try {
            // Create payment record
            $payment = new Payment([
                'booking_id' => $booking->id,
                'payment_method' => 'paypal',
                'amount' => $booking->total_price,
                'status' => 'completed',
                'transaction_id' => 'PP_' . uniqid()
            ]);

            $payment->save();

            // Update booking status
            $booking->update([
                'payment_status' => 'paid',
                'payment_method' => 'paypal',
                'paid_at' => now()
            ]);

            return [
                'success' => true,
                'message' => 'PayPal payment processed successfully',
                'payment' => $payment,
                'booking' => $booking
            ];
        } catch (Exception $e) {
            Log::error('PayPal payment error: ' . $e->getMessage());
            throw new Exception('PayPal payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Process a digital wallet payment
     *
     * @param Booking $booking
     * @param array $paymentData
     * @return array
     */
    protected function processDigitalWalletPayment(Booking $booking, array $paymentData): array
    {
        // In a real application, you would integrate with Apple Pay, Google Pay, etc.
        // Here we're simulating a successful payment

        // Create a payment record
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'payment_method' => 'digital_wallet',
            'transaction_id' => 'wallet_' . uniqid(),
            'status' => 'completed',
        ]);

        // Update booking status
        $booking->update(['payment_status' => 'completed']);

        return [
            'success' => true,
            'message' => 'Digital wallet payment processed successfully',
            'payment' => $payment,
            'booking' => $booking
        ];
    }

    /**
     * Create a Stripe token (for testing purposes)
     *
     * @param array $cardData
     * @return string
     * @throws ApiErrorException
     */
    protected function createStripeToken(array $cardData): string
    {
        $token = $this->stripe->tokens->create([
            'card' => [
                'number' => $cardData['card_number'],
                'exp_month' => substr($cardData['expiry_date'], 0, 2),
                'exp_year' => '20' . substr($cardData['expiry_date'], -2),
                'cvc' => $cardData['cvv'],
                'name' => $cardData['card_name'],
            ],
        ]);

        return $token->id;
    }
}
