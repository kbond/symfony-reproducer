<?php

namespace App\Messenger\Monitor\Worker;

use Symfony\Component\Messenger\WorkerMetadata;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class WorkerStatus
{
    public const IDLE = 'idle';
    public const PROCESSING = 'processing';

    private string $status = self::IDLE;

    /**
     * @internal
     */
    public function __construct(
        private WorkerMetadata $metadata,
    ) {
    }

    public function status(): string
    {
        return $this->status;
    }

    public function transports(): array
    {
        return $this->metadata->getTransportNames();
    }

    public function queues(): array
    {
        return $this->metadata->getQueueNames() ?? [];
    }

    public function isIdle(): bool
    {
        return self::IDLE === $this->status;
    }

    public function isProcessing(): bool
    {
        return self::PROCESSING === $this->status;
    }

    /**
     * @internal
     */
    public function markProcessing(): self
    {
        $this->status = self::PROCESSING;

        return $this;
    }

    /**
     * @internal
     */
    public function markIdle(): self
    {
        $this->status = self::IDLE;

        return $this;
    }
}
