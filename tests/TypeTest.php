<?php

namespace App\Tests;

use App\Messenger\Monitor\Storage;
use App\Messenger\Monitor\Storage\Model\Type;
use PHPUnit\Framework\TestCase;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TypeTest extends TestCase
{
    /**
     * @test
     */
    public function create_from(): void
    {
        $this->assertSame('stdClass', (string) Type::from(new \stdClass()));
        $this->assertSame(Storage::class, (string) Type::from(Storage::class));
        $this->assertSame('foo', (string) Type::from('foo'));
    }

    /**
     * @test
     */
    public function shortName(): void
    {
        $this->assertSame('Storage', Type::from(Storage::class)->shortName());
        $this->assertSame('stdClass', Type::from(new \stdClass())->shortName());
        $this->assertSame('foo', Type::from('foo')->shortName());
        $this->assertSame('foo', Type::from('\\foo')->shortName());
    }
}
