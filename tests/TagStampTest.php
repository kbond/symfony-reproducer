<?php

namespace App\Tests;

use App\Messenger\Monitor\Stamp\TagStamp;
use PHPUnit\Framework\TestCase;

class TagStampTest extends TestCase
{
    /**
     * @test
     */
    public function denormalize(): void
    {
        $this->assertSame([], TagStamp::denormalize(null));
        $this->assertSame(['foo'], TagStamp::denormalize('foo'));
        $this->assertSame(['foo', 'bar'], TagStamp::denormalize('foo,bar'));
        $this->assertSame(['foo', 'schedule', 'schedule:default'], TagStamp::denormalize('foo,schedule:default'));
        $this->assertSame(['foo', 'schedule', 'schedule:default', 'schedule:default:id'], TagStamp::denormalize('foo,schedule:default:id'));
    }
}
