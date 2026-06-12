<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Order;

class ReceiptMailable extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string|null $receiptPath;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, ?string $receiptPath = null)
    {
        $this->order = $order;
        $this->receiptPath = $receiptPath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->subject('Recibo da sua encomenda FunShirt')
                     ->view('emails.receipt')
                     ->with(['order' => $this->order]);

        if ($this->receiptPath && file_exists($this->receiptPath)) {
            $mail->attach($this->receiptPath);
        }

        return $mail;
    }
}
