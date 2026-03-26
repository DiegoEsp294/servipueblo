<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkerContactNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $worker;

    public function __construct(\App\Models\Worker $worker)
    {
        $this->worker = $worker;
    }

    public function build()
    {
        return $this->subject('Alguien quiere contactarte en ServiPueblo')
                    ->markdown('emails.worker-contact');
    }
}
