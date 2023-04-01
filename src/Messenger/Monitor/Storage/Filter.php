<?php

namespace App\Messenger\Monitor\Storage;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Filter
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    public function __construct(
        public readonly \DateTimeImmutable $from,
        public readonly ?\DateTimeImmutable $to,
        public readonly ?string $status,
        public readonly ?string $messageType,
        public readonly ?string $receiver,
        private readonly ?array $tags,
    ) {
    }

    public function tags(): array
    {
        return $this->tags ?? [];
    }
}
