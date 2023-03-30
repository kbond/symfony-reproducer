<?php

namespace App\Messenger\Monitor\Statistics\Metric;

use App\Messenger\Monitor\Statistics\Metric;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class HandledPer extends Metric
{
    public readonly float $intervals;

    private function __construct(\DateTimeImmutable $from, ?\DateTimeImmutable $to, float $multiplier)
    {
        parent::__construct($from, $to);

        $to ??= new \DateTimeImmutable();

        $this->intervals = \abs($from->getTimestamp() - $to->getTimestamp()) / $multiplier;
    }

    public static function hour(\DateTimeImmutable $from, ?\DateTimeImmutable $to = null): static
    {
        return new self($from, $to, 60 * 60);
    }

    public static function day(\DateTimeImmutable $from, ?\DateTimeImmutable $to = null): static
    {
        return new self($from, $to, 60 * 60 * 24);
    }
}
