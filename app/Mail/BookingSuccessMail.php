<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $doctorName;
    public string $patientName;
    public string $recipientName;   
    public string $bookingToken;
    public string $slotDate;
    public string $slotTime;

    public function __construct(
        string $doctorName,
        string $patientName,
        string $recipientName,      
        string $bookingToken,
        string $slotDate,
        string $slotTime
    ) {
        $this->doctorName    = $doctorName;
        $this->patientName   = $patientName;
        $this->recipientName = $recipientName;   
        $this->bookingToken  = $bookingToken;
        $this->slotDate      = $slotDate;
        $this->slotTime      = $slotTime;
    }

    public function build()
    {
        return $this
            ->subject("Booking Confirmed – Ref #{$this->bookingToken}")
            ->markdown('emails.booking-success');
    }
}
