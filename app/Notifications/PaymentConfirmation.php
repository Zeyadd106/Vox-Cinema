<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;

    /**
     * Create a new notification instance.
     *
     * @param Payment $payment
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $booking = $this->payment->booking;
        $movie = $booking->showtime->movie;

        return (new MailMessage)
            ->subject('Payment Confirmation - VOX Cinemas')
            ->greeting('Thank you for your payment!')
            ->line('Your payment has been successfully processed.')
            ->line('Transaction Details:')
            ->line('- Transaction ID: ' . $this->payment->transaction_id)
            ->line('- Amount: $' . number_format($this->payment->amount, 2))
            ->line('- Payment Method: ' . ucfirst($this->payment->payment_method))
            ->line('')
            ->line('Booking Details:')
            ->line('- Movie: ' . $movie->title)
            ->line('- Date: ' . $booking->showtime->date->format('D, M j, Y'))
            ->line('- Time: ' . $booking->showtime->time->format('g:i A'))
            ->line('- Seats: ' . $booking->seats->pluck('name')->implode(', '))
            ->action('View Booking Details', route('bookings.show', $booking->id))
            ->line('Thank you for choosing VOX Cinemas!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'payment_id' => $this->payment->id,
            'booking_id' => $this->payment->booking_id,
            'amount' => $this->payment->amount,
            'transaction_id' => $this->payment->transaction_id,
        ];
    }
}
