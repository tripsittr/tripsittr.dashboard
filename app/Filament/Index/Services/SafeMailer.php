<?php
namespace App\Filament\Index\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SafeMailer
{
    public static function send(string $to, Mailable $mailable, string $context = 'observer'): void
    {
        $suppress = config('mail-safety.suppress_observer_mail');
        $allowed = config('mail-safety.allowed_recipients');

        // If an allow-list is defined, enforce it.
        if (!empty($allowed) && !in_array(strtolower($to), array_map('strtolower', $allowed))) {
            Log::info('SafeMailer suppressed email (not in allow list)', [
                'to' => $to,
                'context' => $context,
                'mailable' => get_class($mailable),
            ]);
            return;
        }

        if ($suppress) {
            Log::info('SafeMailer suppressed email (suppress flag active)', [
                'to' => $to,
                'context' => $context,
                'mailable' => get_class($mailable),
            ]);
            return;
        }

        try {
            Mail::to($to)->send($mailable);
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            // Specific Mailgun sandbox or transport errors -> log instead of bubbling fatal to UI.
            Log::warning('SafeMailer transport exception; email suppressed.', [
                'to' => $to,
                'context' => $context,
                'mailable' => get_class($mailable),
                'error' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::error('SafeMailer unexpected exception.', [
                'to' => $to,
                'context' => $context,
                'mailable' => get_class($mailable),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
