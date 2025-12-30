<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class BookingCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        // pastikan booking sudah berisi relasi yang dibutuhkan:
        // ->load('user','psikiater.user','psikolog.user') dari controller sebelum dikirim
        $this->booking = $booking;
    }

    public function build()
    {
        // subject ringkas
        $subject = 'Booking Baru — ' . config('app.name');

        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($subject)
            ->markdown('emails.booking-created')
            ->with([
                'booking' => $this->booking,
            ]);
    }
}
