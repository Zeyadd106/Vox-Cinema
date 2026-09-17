<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     *
     * @param Booking $booking
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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
        $movie = $this->booking->showtime->movie;
        $showtime = $this->booking->showtime;
        $seats = $this->booking->seats;

        $seatLabels = [];
        foreach ($seats as $seat) {
            $seatLabels[] = $seat->row . $seat->number;
        }

        return (new MailMessage)
            ->subject('Your Vox Cinemas Booking Confirmation')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for booking with Vox Cinemas. Your booking has been confirmed.')
            ->line('**Booking Reference:** ' . $this->booking->booking_reference)
            ->line('**Movie:** ' . $movie->title)
            ->line('**Date:** ' . \Carbon\Carbon::parse($showtime->date)->format('l, F j, Y'))
            ->line('**Time:** ' . \Carbon\Carbon::parse($showtime->time)->format('h:i A'))
            ->line('**Seats:** ' . implode(', ', $seatLabels))
            ->line('**Total Amount:** $' . number_format($this->booking->total_price, 2))
            ->action('View Booking Details', url('/bookings/' . $this->booking->id))
            ->line('Please arrive at least 15 minutes before the showtime. You can present your booking reference or QR code at the cinema.')
            ->line('Enjoy the movie!');
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
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'movie' => $this->booking->showtime->movie->title,
            'date' => $this->booking->showtime->date,
            'time' => $this->booking->showtime->time,
        ];
    }
}
