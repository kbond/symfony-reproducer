<?php

namespace App\Messenger\Monitor\Storage;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Specification
{
    public const SUCCESS = 'success';
    public const FAILED = 'failed';

    private ?\DateTimeImmutable $from = null;
    private ?\DateTimeImmutable $to = null;
    private ?string $status = null;
    private ?string $messageType = null;
    private ?string $transport = null;
    private array $tags = [];

    public static function new(): self
    {
        return new self();
    }

    /**
     * @param array{
     *     from?: \DateTimeImmutable|string|null,
     *     to?: \DateTimeImmutable|string|null,
     *     status?: ?string,
     *     message_type?: ?string,
     *     transport?: ?string,
     *     tags?: ?array
     * } $values
     */
    public static function fromArray(array $values): self
    {
        $specification = new self();
        $specification->messageType = $values['message_type'] ?? null;
        $specification->transport = $values['transport'] ?? null;
        $specification->tags = $values['tags'] ?? [];
        $specification->status = match($values['status'] ?? null) {
            self::SUCCESS => self::SUCCESS,
            self::FAILED => self::FAILED,
            default => null,
        };

        if ($values['from'] ?? null) {
            $specification = $specification->from($values['from']);
        }

        if ($values['to'] ?? null) {
            $specification = $specification->to($values['to']);
        }

        return $specification;
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

    public function on(string $transport): self
    {
        $clone = clone $this;
        $clone->transport = $transport;

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
     *     transport: ?string,
     *     tags: string[]
     * }
     */
    public function toArray(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'status' => $this->status,
            'message_type' => $this->messageType,
            'transport' => $this->transport,
            'tags' => $this->tags,
        ];
    }
}
