<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ModelCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $model;

    public function __construct($model = null)
    {
        $this->model = $model;
    }

    public function build()
    {
        return $this->view('emails.fallback')
            ->with(['model' => $this->model]);
    }
}
