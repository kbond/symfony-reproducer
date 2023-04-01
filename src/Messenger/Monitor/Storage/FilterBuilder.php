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
        $this->messageType = $messageType;

        return $this;
    }

    public function on(string $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function with(string ...$tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function successes(): self
    {
        $this->status = Filter::STATUS_SUCCESS;

        return $this;
    }

    public function failures(): self
    {
        $this->status = Filter::STATUS_FAILED;

        return $this;
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
