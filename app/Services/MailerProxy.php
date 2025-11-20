<?php

namespace App\Services;

use Psr\Log\LoggerInterface;

/**
 * Simple proxy/decorator around the framework mailer to suppress and log
 * exceptions thrown during mail sending. This prevents mail view/rendering
 * failures from bubbling up and breaking synchronous HTTP requests.
 */
class MailerProxy
{
    protected $mailer;
    protected $logger;

    public function __construct($mailer, LoggerInterface $logger = null)
    {
        $this->mailer = $mailer;
        $this->logger = $logger ?: app(LoggerInterface::class);
    }

    /**
     * Wrap send so we can catch view/render/transport exceptions.
     */
    public function send(...$args)
    {
        try {
            return $this->mailer->send(...$args);
        } catch (\Throwable $e) {
            $this->logger->error('MailerProxy suppressed exception during send', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            // Swallow the exception to avoid breaking the request. Return null.
            return null;
        }
    }

    /**
     * Wrap queue similarly (queued jobs may still surface errors, but keep safe).
     */
    public function queue(...$args)
    {
        try {
            return $this->mailer->queue(...$args);
        } catch (\Throwable $e) {
            $this->logger->warning('MailerProxy suppressed exception during queue', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
            return null;
        }
    }

    /**
     * Fallback pass-through for other methods/properties.
     */
    public function __call($method, $args)
    {
        return $this->mailer->{$method}(...$args);
    }

    public function __get($name)
    {
        return $this->mailer->{$name};
    }

    public function __set($name, $value)
    {
        $this->mailer->{$name} = $value;
    }
}
