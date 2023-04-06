<?php

namespace App\Messenger\Monitor\Storage\Model;

use App\Messenger\Monitor\Stamp\MonitorStamp;
use App\Messenger\Monitor\Stamp\TagStamp;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use function Symfony\Component\Clock\now;

#[ORM\MappedSuperclass]
abstract class ProcessedMessage
{
    #[ORM\Column(length: 255)]
    private string $class;

    #[ORM\Column]
    private \DateTimeImmutable $dispatchedAt;

    #[ORM\Column]
    private \DateTimeImmutable $receivedAt;

    #[ORM\Column]
    private \DateTimeImmutable $handledAt;

    #[ORM\Column]
    private string $transport;

    #[ORM\Column(nullable: true)]
    private ?string $error = null;

    #[ORM\Column(nullable: true)]
    private ?string $tags;

    private function __construct()
    {
    }

    public static function create(Envelope $envelope, ?\Throwable $exception = null): static
    {
        $monitorStamp = $envelope->last(MonitorStamp::class) ?? throw new \LogicException('Required stamp not available');

        $object = new static();
        $object->class = $envelope->getMessage()::class; // todo use zenstruck/class-metadata
        $object->dispatchedAt = $monitorStamp->dispatchedAt;
        $object->receivedAt = $monitorStamp->receivedAt();
        $object->handledAt = now();
        $object->transport = $monitorStamp->transport();
        $object->tags = TagStamp::normalize($envelope);

        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious() ?? $exception;
        }

        if ($exception) {
            $object->error = \sprintf('%s: %s', $exception::class, $exception->getMessage());
        }

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

    final public function transport(): string
    {
        return $this->transport;
    }

    final public function tags(): array
    {
        return TagStamp::denormalize($this->tags);
    }

    final public function error(): ?string
    {
        return $this->error;
    }

    final public function isError(): bool
    {
        return null !== $this->error;
    }

    final public function timeInQueue(): int
    {
        return \max(0, $this->receivedAt->getTimestamp() - $this->dispatchedAt->getTimestamp());
    }

    final public function timeToHandle(): int
    {
        return \max(0, $this->handledAt->getTimestamp() - $this->receivedAt->getTimestamp());
    }

    final public function timeToProcess(): int
    {
        return \max(0, $this->handledAt->getTimestamp() - $this->dispatchedAt->getTimestamp());
    }
}
