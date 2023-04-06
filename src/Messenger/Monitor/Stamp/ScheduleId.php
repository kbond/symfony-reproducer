<?php

namespace App\Messenger\Monitor\Stamp;

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

    public function for(RecurringMessage $recurringMessage): self
    {
        $message = $recurringMessage->getMessage();
        $trigger = $recurringMessage->getTrigger();

        return new self(\hash('crc32c', \implode('', [
            $message instanceof \Stringable ? (string) $message : \serialize($message),
            $trigger instanceof \Stringable ? (string) $trigger : \serialize($trigger),
        ])));
    }
}
