<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customer,$token, $sender;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Customer $customer, $token, $sender)
    {
        $this->sender = $sender;
        $this->token = $token;
        $this->customer = $customer;
        // $this->subject = $subject;
        // $this->view = $view;
        // $this->message = $message;
    }

    public function build()
    {
        return $this->subject('Confirm Your E-mail Address')
        ->view('mail-template.welcome')
        ->from($this->sender)
        ->with([
            'customer' => $this->customer, // Pass any data you want to include in the email template
            'confirmation_url' => url('api/v1/customers/email-verification?token='.$this->token)
        ]);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    // public function envelope()
    // {
    //     return new Envelope(
    //         subject: 'Welcome Mail',
    //     );
    // }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    // public function content()
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    // public function attachments()
    // {
    //     return [];
    // }
}
