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
    private string $transport;
    private \DateTimeImmutable $receivedAt;

    public function __construct()
    {
        $this->dispatchedAt = now();
    }

    public function markReceived(string $transport): self
    {
        $this->transport = $transport;
        $this->receivedAt = now();

        return $this;
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
