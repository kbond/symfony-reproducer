<?php

namespace App\Messenger\Monitor\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

use function Symfony\Component\Clock\now;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class MonitorStamp implements StampInterface
{
    public readonly \DateTimeImmutable $dispatchedAt;
    private string $receiver;
    private \DateTimeImmutable $receivedAt;

    public function __construct()
    {
        $this->dispatchedAt = now();
    }

    public function markReceived(string $receiver): self
    {
        $this->receiver = $receiver;
        $this->receivedAt = now();

        return $this;
    }

    public function receiver(): string
    {
        return $this->receiver ?? throw new \LogicException('Message not yet received.');
    }

    public function receivedAt(): \DateTimeImmutable
    {
        return $this->receivedAt ?? throw new \LogicException('Message not yet received.');
    }
}
