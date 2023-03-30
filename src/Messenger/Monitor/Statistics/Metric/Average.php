<?php

namespace App\Messenger\Monitor\Statistics\Metric;

use App\Messenger\Monitor\Statistics\Metric;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Average extends Metric
{
    public const WAIT = 'wait';
    public const HANDLE = 'handle';

    public readonly ?string $type;

    private function __construct(\DateTimeImmutable $from, ?\DateTimeImmutable $to, ?string $type)
    {
        parent::__construct($from, $to);

        $this->type = $type;
    }

    public static function waitTime(\DateTimeImmutable $from, ?\DateTimeImmutable $to = null): self
    {
        return new self($from, $to, self::WAIT);
    }

    public static function handlingTime(\DateTimeImmutable $from, ?\DateTimeImmutable $to = null): self
    {
        return new self($from, $to, self::HANDLE);
    }

    public static function totalProcessingTime(\DateTimeImmutable $from, ?\DateTimeImmutable $to = null): self
    {
        return new self($from, $to, null);
    }
}
