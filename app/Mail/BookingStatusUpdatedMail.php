<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class BookingStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var Booking */
    public $booking;

    /** @var string|null */
    public $providerName;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;

        // safe resolution of provider name (psikiater OR psikolog)
        $name = null;
        try {
            if ($booking->relationLoaded('psikiater') || method_exists($booking, 'psikiater')) {
                $name = optional($booking->psikiater)->name;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        if (! $name) {
            try {
                if ($booking->relationLoaded('psikolog') || method_exists($booking, 'psikolog')) {
                    $name = optional($booking->psikolog)->name;
                } else {
                    // try to read via DB fallback if you don't have model relation
                    if (isset($booking->psikolog_id) && $booking->psikolog_id) {
                        $row = \DB::table('psikologs')->where('id', $booking->psikolog_id)->first();
                        $name = $row->name ?? null;
                    }
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $this->providerName = $name;
    }

    public function build()
    {
        $status = ucfirst($this->booking->status ?? '');
        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject("Perubahan Status Booking: {$status} — " . config('app.name'))
            ->markdown('emails.booking-status-update')
            ->with([
                'booking' => $this->booking,
                'providerName' => $this->providerName,
            ]);
    }
}
