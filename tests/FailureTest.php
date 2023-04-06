<?php

namespace App\Tests;

use App\Messenger\Monitor\Storage\Model\Failure;
use PHPUnit\Framework\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class FailureTest extends TestCase
{
    /**
     * @test
     */
    public function create_from(): void
    {
        $this->assertSame('foo: bar', (string) Failure::from('foo: bar'));
        $this->assertSame('RuntimeException: message', (string) Failure::from(new \RuntimeException('message')));
    }

    /**
     * @test
     */
    public function can_access_exception_if_created_with_throwable(): void
    {
        $exception = new \RuntimeException('message');

        $this->assertSame($exception, Failure::from($exception)->exception());
    }

    /**
     * @test
     */
    public function cannot_access_exception_if_created_with_string(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Exception is not available after it\'s been stored.');

        Failure::from('foo: bar')->exception();
    }
}
