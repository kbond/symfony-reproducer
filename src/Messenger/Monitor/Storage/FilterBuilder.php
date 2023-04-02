<?php

namespace App\Messenger\Monitor\Storage;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FilterBuilder
{
    private string $status;
    private string $messageType;
    private string $receiver;
    private array $tags;

    public function __construct(
        private \DateTimeImmutable $from,
        private  ?\DateTimeImmutable $to = null,
    ) {
    }

    public static function lastHour(): self
    {
        return new self(new \DateTimeImmutable('-1 hour'));
    }

    public static function lastDay(): self
    {
        return new self(new \DateTimeImmutable('-1 day'));
    }

    public static function lastWeek(): self
    {
        return new self(new \DateTimeImmutable('-7 days'));
    }

    public static function lastMonth(): self
    {
        return new self(new \DateTimeImmutable('-1 month'));
    }

    /**
     * @param array{
     *     from: \DateTimeImmutable,
     *     to?: ?\DateTimeImmutable,
     *     status?: ?string,
     *     messageType?: ?string,
     *     receiver?: ?string,
     *     tags?: ?array
     * } $values
     */
    public static function fromArray(array $values): self
    {
        $filter = new self($values['from'], $values['to'] ?? null);

        if ($values['status'] ?? null) {
            $filter->status = $values['status'];
        }

        if ($values['messageType'] ?? null) {
            $filter->messageType = $values['messageType'];
        }

        if ($values['receiver'] ?? null) {
            $filter->receiver = $values['receiver'];
        }

        if ($values['tags'] ?? null) {
            $filter->tags = $values['tags'];
        }

        return $filter;
    }

    public function for(string $messageType): self
    {
        $clone = clone $this;
        $clone->messageType = $messageType;

        return $clone;
    }

    public function on(string $receiver): self
    {
        $clone = clone $this;
        $clone->receiver = $receiver;

        return $clone;
    }

    public function with(string ...$tags): self
    {
        $clone = clone $this;
        $clone->tags = $tags;

        return $clone;
    }

    public function successes(): self
    {
        $clone = clone $this;
        $clone->status = Filter::STATUS_SUCCESS;

        return $clone;
    }

    public function failures(): self
    {
        $clone = clone $this;
        $clone->status = Filter::STATUS_FAILED;

        return $clone;
    }

    public function build(): Filter
    {
        return new Filter(
            $this->from,
            $this->to,
            $this->status ?? null,
            $this->messageType ?? null,
            $this->receiver ?? null,
            $this->tags ?? null,
        );
    }
}
