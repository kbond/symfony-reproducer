<?php

namespace App\Messenger\Monitor\Model;

use App\Messenger\Monitor\Stamp\MonitorStamp;
use App\Messenger\Monitor\Stamp\TagStamp;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Messenger\Envelope;
use function Symfony\Component\Clock\now;

#[ORM\MappedSuperclass]
class StoredMessage
{
    #[ORM\Column(length: 255)]
    private string $class;

    #[ORM\Column]
    private \DateTimeImmutable $dispatchedAt;

    #[ORM\Column]
    private \DateTimeImmutable $receivedAt;

    #[ORM\Column]
    private \DateTimeImmutable $handledAt;

    #[ORM\Column(length: 255)]
    private string $receiver;

    #[ORM\Column]
    private array $tags;

    private function __construct()
    {
    }

    public static function create(Envelope $envelope): static
    {
        $monitorStamp = $envelope->last(MonitorStamp::class) ?? throw new \LogicException('Required stamp not available');

        $object = new static();
        $object->class = $envelope->getMessage()::class; // todo use zenstruck/class-metadata
        $object->dispatchedAt = $monitorStamp->dispatchedAt;
        $object->receivedAt = $monitorStamp->receivedAt();
        $object->handledAt = now();
        $object->receiver = $monitorStamp->receiver();
        $object->tags = TagStamp::parse($envelope);

        return $object;
    }

    final public function class(): string
    {
        return $this->class;
    }

    final public function dispatchedAt(): \DateTimeImmutable
    {
        return $this->dispatchedAt;
    }

    final public function receivedAt(): \DateTimeImmutable
    {
        return $this->receivedAt;
    }

    final public function handledAt(): \DateTimeImmutable
    {
        return $this->handledAt;
    }

    final public function receiver(): string
    {
        return $this->receiver;
    }

    final public function tags(): array
    {
        return $this->tags;
    }
}
