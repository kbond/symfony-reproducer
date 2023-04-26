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
    private \DateTimeImmutable $dispatchedAt;
    private string $transport;
    private \DateTimeImmutable $receivedAt;

    public function __construct(?\DateTimeImmutable $dispatchedAt = null)
    {
        $this->dispatchedAt = $dispatchedAt ?? now();
    }

    public function markReceived(string $transport): self
    {
        $clone = clone $this;
        $clone->transport = $transport;
        $clone->receivedAt = now();

        return $clone;
    }

    public function dispatchedAt(): \DateTimeImmutable
    {
        return $this->dispatchedAt;
    }

    public function transport(): string
    {
        return $this->transport ?? throw new \LogicException('Message not yet received.');
    }

    public function receivedAt(): \DateTimeImmutable
    {
        return $this->receivedAt ?? throw new \LogicException('Message not yet received.');
    }
}
