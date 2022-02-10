<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActiveUserMail extends Mailable
{
  use Queueable, SerializesModels;
  public $email;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($email)
  {
    //
    $this->email = $email;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
//        return $this->view('view.name');
    return $this->markdown('emails.activeUser')->with([
      'email' => $this->email
    ]);
  }
}
