<?php

namespace App\Messenger\Monitor\Storage\Model;

use App\Messenger\Monitor\Stamp\MonitorStamp;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Messenger\Envelope;
use function Symfony\Component\Clock\now;

#[ORM\MappedSuperclass]
abstract class ProcessedMessage
{
    #[ORM\Column]
    private string|Type $type;

    #[ORM\Column]
    private \DateTimeImmutable $dispatchedAt;

    #[ORM\Column]
    private \DateTimeImmutable $receivedAt;

    #[ORM\Column]
    private \DateTimeImmutable $handledAt;

    #[ORM\Column]
    private string $transport;

    #[ORM\Column(nullable: true)]
    private string|Failure|null $failure = null;

    #[ORM\Column(nullable: true)]
    private string|Tags|null $tags;

    private function __construct()
    {
    }

    public static function create(Envelope $envelope, ?\Throwable $exception = null): static
    {
        $monitorStamp = $envelope->last(MonitorStamp::class) ?? throw new \LogicException('Required stamp not available');

        $object = new static();
        $object->type = Type::from($envelope->getMessage());
        $object->dispatchedAt = $monitorStamp->dispatchedAt;
        $object->receivedAt = $monitorStamp->receivedAt();
        $object->handledAt = now();
        $object->transport = $monitorStamp->transport();
        $object->tags = Tags::from($envelope);

        if ($exception) {
            $object->failure = Failure::from($exception);
        }

        return $object;
    }

    final public function type(): Type
    {
        if ($this->type instanceof Type) {
            return $this->type;
        }

        return $this->type = new Type($this->type);
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

    final public function failure(): Failure
    {
        if ($this->failure instanceof Failure) {
            return $this->failure;
        }

        if (!$this->failure) {
            throw new \LogicException(\sprintf('Message "%s" has no failure', $this->type()));
        }

        return $this->failure = Failure::from($this->failure);
    }

    final public function isFailure(): bool
    {
        return null !== $this->failure;
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
