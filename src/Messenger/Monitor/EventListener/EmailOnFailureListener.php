<?php

namespace App\Messenger\Monitor\EventListener;

use App\Messenger\Monitor\Model\ProcessedMessage;
use App\Messenger\Monitor\Stamp\EmailOnFailure;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
#[AsEventListener]
final class EmailOnFailureListener
{
    public function __construct(
        private MailerInterface $mailer,
        private ?EmailOnFailure $default = null,
        private bool $lastStampOnly = false,
        private bool $alwaysSendToDefault = false,
    ) {
        if ($this->alwaysSendToDefault && !$this->default) {
            throw new \InvalidArgumentException('You must provide a default alwaysSendToDefault is true.');
        }

        if ($this->alwaysSendToDefault && !$this->default->hasTo()) {
            throw new \InvalidArgumentException('You must provide a "to" address for the default when alwaysSendToDefault is true.');
        }
    }

    public function __invoke(WorkerMessageFailedEvent $event): void
    {
        $message = ProcessedMessage::create($envelope = $event->getEnvelope(), $event->getThrowable());
        $stamps = $this->lastStampOnly ? \array_filter([$envelope->last(EmailOnFailure::class)]) : EmailOnFailure::from($envelope);

        foreach ($stamps as $stamp) {
            $this->mailer->send($stamp->createEmail($message, $this->default));
        }

        if ($this->alwaysSendToDefault) {
            $this->mailer->send($this->default->createEmail($message));
        }
    }
}
