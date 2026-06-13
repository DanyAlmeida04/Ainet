<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\TshirtImage;
use App\Models\User;

class NewDesignNotificationMailable extends Mailable
{
    use Queueable, SerializesModels;

    public TshirtImage $design;
    public User $customer;

    /**
     * Create a new message instance.
     */
    public function __construct(TshirtImage $design, User $customer)
    {
        $this->design = $design;
        $this->customer = $customer;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Nova estampa disponível no catálogo: ' . $this->design->name)
                    ->view('emails.new_design')
                    ->with([
                        'design' => $this->design,
                        'customer' => $this->customer,
                    ]);
    }
}
