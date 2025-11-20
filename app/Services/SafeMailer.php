<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class SafeMailer
{
    public static function send($to, $mailable, $type = null)
    {
        // You can replace this with actual mail logic
        try {
            Mail::to($to)->send($mailable);
        } catch (\Throwable $e) {
            // Log or handle error as needed
        }
    }
}
