<?php

namespace App\Messenger\Monitor\Storage;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Filter
{
    public const SUCCESS = 'success';
    public const FAILED = 'failed';

    private \DateTimeImmutable $from;
    private \DateTimeImmutable $to;
    private string $status;
    private string $messageType;
    private string $receiver;
    private array $tags;

    public static function new(): self
    {
        return new self();
    }

    public static function range(string|\DateTimeImmutable $from, string|\DateTimeImmutable|null $to = null): self
    {
        $filter = self::new()->from($from);

        return $to ? $filter->to($to) : $filter;
    }

    public static function lastHour(): self
    {
        return self::range('-1 hour');
    }

    public static function lastDay(): self
    {
        return self::range('-1 day');
    }

    public static function lastWeek(): self
    {
        return self::range('-7 days');
    }

    public static function lastMonth(): self
    {
        return self::range('-1 month');
    }

    /**
     * @param array{
     *     from?: \DateTimeImmutable|string|null,
     *     to?: \DateTimeImmutable|string|null,
     *     status?: ?string,
     *     message_type?: ?string,
     *     receiver?: ?string,
     *     tags?: ?array
     * } $values
     */
    public static function fromArray(array $values): self
    {
        $filter = new self();

        if ($values['from'] ?? null) {
            $filter->from = $values['from'];
        }

        if ($values['to'] ?? null) {
            $filter->to = $values['to'];
        }

        if ($values['status'] ?? null) {
            $filter->status = $values['status'];
        }

        if ($values['message_type'] ?? null) {
            $filter->messageType = $values['message_type'];
        }

        if ($values['receiver'] ?? null) {
            $filter->receiver = $values['receiver'];
        }

        if ($values['tags'] ?? null) {
            $filter->tags = $values['tags'];
        }

        return $filter;
    }

    public function from(string|\DateTimeImmutable $value): self
    {
        $clone = clone $this;
        $clone->from = $value instanceof \DateTimeImmutable ? $value : new \DateTimeImmutable($value);

        return $clone;
    }

    public function to(string|\DateTimeImmutable $value): self
    {
        $clone = clone $this;
        $clone->to = $value instanceof \DateTimeImmutable ? $value : new \DateTimeImmutable($value);

        return $clone;
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
        $clone->status = self::SUCCESS;

        return $clone;
    }

    public function failures(): self
    {
        $clone = clone $this;
        $clone->status = self::FAILED;

        return $clone;
    }

    /**
     * @return array{
     *     from: ?\DateTimeImmutable,
     *     to: ?\DateTimeImmutable,
     *     status: ?string,
     *     message_type: ?string,
     *     receiver: ?string,
     *     tags: string[]
     * }
     */
    public function toArray(): array
    {
        return [
            'from' => $this->from ?? null,
            'to' => $this->to ?? null,
            'status' => $this->status ?? null,
            'message_type' => $this->messageType ?? null,
            'receiver' => $this->receiver ?? null,
            'tags' => $this->tags ?? [],
        ];
    }
}
