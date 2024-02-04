<?php

namespace App\Assert\Handler;

use App\Assert\AssertionFailed;
use App\Assert\Handler;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Util\ExcludeList;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class PHPUnitHandler implements Handler
{
    public function __construct()
    {
        ExcludeList::addDirectory(__DIR__);
    }

    public function onSuccess(): void
    {
        // trigger a successful PHPUnit assertion to avoid "risky" tests
        PHPUnit::assertTrue(true);
    }

    public function onFailure(AssertionFailed $exception): void
    {
        PHPUnit::fail($exception->getMessage().self::comparisonDiff($exception));
    }

    public static function isSupported(): bool
    {
        return class_exists(PHPUnit::class);
    }

    private static function comparisonDiff(AssertionFailed $exception): string
    {
        $expected = $exception->context['expected'] ?? null;
        $actual = $exception->context['actual'] ?? null;

        try {
            ComparatorFactory::getInstance()
                ->getComparatorFor($expected, $actual)
                ->assertEquals($expected, $actual)
            ;
        } catch (ComparisonFailure $e) {
            return $e->getDiff();
        }

        return '';
    }
}
