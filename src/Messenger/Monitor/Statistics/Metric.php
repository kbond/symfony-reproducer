<?php

namespace App\Messenger\Monitor\Statistics;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Metric
{
    final public const STATUS_SUCCESS = 'success';
    final public const STATUS_FAILED = 'failed';

    public readonly string $status;
    public readonly string $messageType;
    public readonly string $receiver;
    public readonly array $tags;

    public function __construct(
        public readonly \DateTimeImmutable $from,
        public readonly ?\DateTimeImmutable $to
    ) {
    }

    final public function for(string $messageType): static
    {
        $this->messageType = $messageType;

        return $this;
    }

    final public function on(string $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    final public function with(string ...$tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    final public function successes(): static
    {
        $this->status = self::STATUS_SUCCESS;

        return $this;
    }

    final public function failures(): static
    {
        $this->status = self::STATUS_FAILED;

        return $this;
    }
}
