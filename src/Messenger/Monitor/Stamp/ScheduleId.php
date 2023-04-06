<?php

namespace App\Messenger\Monitor\Stamp;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Scheduler\RecurringMessage;

/**
 * TODO - remove if https://github.com/symfony/symfony/pull/49838 and https://github.com/symfony/symfony/pull/49865 are merged
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ScheduleId implements StampInterface
{
    public function __construct(
        public readonly string $id,
    ) {
    }

    public static function wrap(RecurringMessage $message, ?string $id = null): RecurringMessage
    {
        $envelope = Envelope::wrap($message->getMessage());

        if (!$id && $envelope->last(self::class)) {
            return $message;
        }

        $envelope = $envelope->with(self::for($message, $id));

        return RecurringMessage::trigger($message->getTrigger(), $envelope);
    }

    private static function for(RecurringMessage $recurringMessage, ?string $id = null): self
    {
        if ($id) {
            return new self($id);
        }

        $message = $recurringMessage->getMessage();
        $trigger = $recurringMessage->getTrigger();

        if ($message instanceof Envelope) {
            $message = $message->getMessage();
        }

        return new self(\hash('crc32c', \implode('', [
            $message instanceof \Stringable ? (string) $message : \serialize($message),
            $trigger instanceof \Stringable ? (string) $trigger : \serialize($trigger),
        ])));
    }
}
