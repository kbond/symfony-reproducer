<?php

namespace App\Messenger\Monitor\Stamp;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class TagStamp implements StampInterface
{
    public readonly array $tags;

    public function __construct(string ...$tags)
    {
        $this->tags = $tags;
    }

    /**
     * @internal
     */
    public static function parse(Envelope $envelope): array
    {
        $tags = [];

        foreach ((new \ReflectionClass($envelope->getMessage()))->getAttributes(self::class, \ReflectionAttribute::IS_INSTANCEOF) as $stamp) {
            $tags[] = $stamp->newInstance()->tags;
        }

        foreach ($envelope->all(self::class) as $stamp) {
            $tags[] = $stamp->tags;
        }

        return \array_filter(\array_unique(\array_merge(...$tags)));
    }
}
