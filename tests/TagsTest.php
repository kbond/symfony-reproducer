<?php

namespace App\Tests;

use App\Messenger\Monitor\Model\Tags;
use App\Messenger\Monitor\Stamp\Tag;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TagsTest extends TestCase
{
    /**
     * @test
     */
    public function create_from_string(): void
    {
        $tags = new Tags('foo, bar,foo  ,baz,   ,,, ');

        $this->assertSame(['foo', 'bar', 'baz'], $tags->all());
    }

    /**
     * @test
     */
    public function create_from_null(): void
    {
        $this->assertSame([], (new Tags(null))->all());
    }

    /**
     * @test
     */
    public function create_from_array(): void
    {
        $tags = new Tags(['foo', 'bar', 'foo  ', 'baz', ' ', '']);

        $this->assertSame(['foo', 'bar', 'baz'], $tags->all());
    }

    /**
     * @test
     */
    public function create_from_envelope(): void
    {
        $envelope = new Envelope(new DummyMessage(), [
            new Tag('foo', 'bar'),
            new Tag('bar', 'baz'),
            new Tag('qux')
        ]);

        $this->assertSame(['foo', 'bar', 'baz', 'qux'], Tags::from($envelope)->all());
    }

    /**
     * @test
     */
    public function iterable_stringable_countable(): void
    {
        $tags = new Tags(['foo', 'bar', 'baz']);

        $this->assertSame('', (string) new Tags());
        $this->assertSame('foo,bar,baz', (string) $tags);
        $this->assertSame(['foo', 'bar', 'baz'], \iterator_to_array($tags));
        $this->assertCount(3, $tags);
    }

    /**
     * @test
     */
    public function implode(): void
    {
        $this->assertNull((new Tags())->implode());
        $this->assertSame('foo', (new Tags(['foo']))->implode());
        $this->assertSame('foo,bar', (new Tags(['foo', 'bar']))->implode());
        $this->assertSame('foo,bar,baz', (new Tags(['foo', 'bar', 'baz']))->implode());
        $this->assertSame('foo-bar-baz', (new Tags(['foo', 'bar', 'baz']))->implode('-'));
    }

    /**
     * @test
     */
    public function expand(): void
    {
        $this->assertSame([], (new Tags())->expand()->all());
        $this->assertSame(['foo'], (new Tags(['foo']))->expand()->all());
        $this->assertSame(['foo', 'bar'], (new Tags(['foo', 'bar']))->expand()->all());
        $this->assertSame(['foo', 'schedule', 'schedule:default'], (new Tags(['foo', 'schedule:default']))->expand()->all());
        $this->assertSame(['foo', 'schedule', 'schedule:default', 'schedule:default:id'], (new Tags(['foo', 'schedule:default:id']))->expand()->all());
        $this->assertSame('foo,schedule,schedule:default,schedule:default:id', (new Tags(['foo', 'schedule:default:id']))->expand()->implode());
    }
}

#[Tag('foo', 'bar')]
#[Tag('bar', 'baz')]
class DummyMessage
{
}
