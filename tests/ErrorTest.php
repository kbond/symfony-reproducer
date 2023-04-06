<?php

namespace App\Tests;

use App\Messenger\Monitor\Storage\Model\Error;
use PHPUnit\Framework\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ErrorTest extends TestCase
{
    /**
     * @test
     */
    public function create_from(): void
    {
        $this->assertSame('foo: bar', (string) Error::from('foo: bar'));
        $this->assertSame('RuntimeException: message', (string) Error::from(new \RuntimeException('message')));
    }
}
