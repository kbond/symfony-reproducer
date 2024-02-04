<?php

namespace App\Tests;

use App\Assert\Expectations;
use PHPUnit\Framework\TestCase;

class ExpectationTest extends TestCase
{
    use Expectations;

    /**
     * @test
     */
    public function to_equal_pass(): void
    {
        $this->expect('foo')->toEqual('foo');
    }

    /**
     * @test
     */
    public function to_equal_fail(): void
    {
        $this->expect('foo')->toEqual('bar');
    }

    /**
     * @test
     */
    public function not_to_equal_pass(): void
    {
        $this->expect('foo')->not()->toEqual('bar');
    }

    /**
     * @test
     */
    public function not_to_equal_fail(): void
    {
        $this->expect('foo')->not()->toEqual('foo');
    }

    /**
     * @test
     */
    public function chain_test(): void
    {
        $this->expect([])
            ->toEqual([])
            ->and()
            ->toBe([])
            ->and()
            ->not()->toBe('string')
            ->and()
            ->not()->toBe('string')
            ->and('foo')
            ->toBe('foo')
            ->and()
            ->not()->toBe('bar')
        ;

        $this->expect([1, 2])
            ->count()
            ->toBe(2)
            ->andNot()->toBeGreaterThan(2)
        ;
    }
}
