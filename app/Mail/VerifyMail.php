<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyMail extends Mailable
{
  use Queueable, SerializesModels;
  public $user;
  public $verification_code;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($user, $verification_code)
  {
    //
    $this->user = $user;
    $this->verification_code = $verification_code;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    $email = $this->markdown('emails.confirmEmail')
      ->with(['user' => $this->user, 'verification_code' => $this->verification_code]);
    return $email;
  }
}
