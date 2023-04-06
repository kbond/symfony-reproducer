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
    public function __construct(private MailerInterface $mailer, private ?EmailOnFailure $default = null)
    {
    }

    public function __invoke(WorkerMessageFailedEvent $event): void
    {
        $message = ProcessedMessage::create($event->getEnvelope(), $event->getThrowable());

        foreach (EmailOnFailure::from($event->getEnvelope()) as $stamp) {
            $this->mailer->send($stamp->createEmail($message, $this->default));
        }
    }
}
