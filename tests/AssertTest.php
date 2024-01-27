<?php

namespace App\Tests;

use App\Assert;
use PHPUnit\Framework\TestCase;

class AssertTest extends TestCase
{
    /**
     * @test
     */
    public function to_equal_pass(): void
    {
        $expected = 'foo';
        $actual = 'foo';

        Assert::true($expected == $actual, 'Expected {expected} to equal {actual}', [
            'expected' => $expected,
            'actual' => $actual,
        ]);
    }

    /**
     * @test
     */
    public function to_equal_fail(): void
    {
        $expected = 'bar';
        $actual = 'foo';

        Assert::true($expected == $actual, 'Expected {expected} to equal {actual}', [
            'expected' => $expected,
            'actual' => $actual,
        ]);
    }

    /**
     * @test
     */
    public function not_to_equal_pass(): void
    {
        $expected = 'bar';
        $actual = 'foo';

        Assert::false($expected == $actual, 'Expected {expected} to NOT equal {actual}', [
            'expected' => $expected,
            'actual' => $actual,
        ]);
    }

    /**
     * @test
     */
    public function not_to_equal_fail(): void
    {
        $expected = 'foo';
        $actual = 'foo';

        Assert::false($expected == $actual, 'Expected {expected} to NOT equal {actual}', [
            'expected' => $expected,
            'actual' => $actual,
        ]);
    }
}
