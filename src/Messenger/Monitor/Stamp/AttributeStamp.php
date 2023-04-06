<?php

namespace App\Messenger\Monitor\Stamp;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class AttributeStamp implements StampInterface
{
    /**
     * @internal
     *
     * @return static[]
     */
    final public static function from(Envelope $envelope): iterable
    {
        foreach ((new \ReflectionClass($envelope->getMessage()))->getAttributes(static::class, \ReflectionAttribute::IS_INSTANCEOF) as $stamp) {
            yield $stamp->newInstance();
        }

        foreach ($envelope->all(static::class) as $stamp) {
            yield $stamp;
        }
    }
}
