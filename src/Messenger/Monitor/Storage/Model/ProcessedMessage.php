<?php

namespace App\Messenger\Monitor\Storage\Model;

use App\Messenger\Monitor\Stamp\MonitorStamp;
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
    private string|Error|null $error = null;

    #[ORM\Column(nullable: true)]
    private string|Tags|null $tags;

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
        $object->tags = Tags::from($envelope);

        if ($exception instanceof HandlerFailedException) {
            $exception = $exception->getPrevious() ?? $exception;
        }

        if ($exception) {
            $object->error = Error::from($exception);
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

    final public function tags(): Tags
    {
        if ($this->tags instanceof Tags) {
            return $this->tags;
        }

        return $this->tags = new Tags($this->tags);
    }

    final public function error(): ?Error
    {
        if (null === $this->error || $this->error instanceof Error) {
            return $this->error;
        }

        return $this->error = Error::from($this->error);
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
