<?php

namespace App\Messenger\Monitor\Statistics\Metric\Calculator;

use App\Messenger\Monitor\Statistics\Metric;
use App\Messenger\Monitor\Statistics\Metric\Calculator;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class MultiCalculator implements Calculator
{
    public function __construct(private readonly ContainerInterface $calculators)
    {
    }

    public function calculate(Metric $metric): float
    {
        try {
            return $this->calculators->get($metric::class)->calculate($metric);
        } catch (NotFoundExceptionInterface $e) {
            throw new \RuntimeException(sprintf('No calculator found for metric "%s".', $metric::class), previous: $e);
        }
    }
}
