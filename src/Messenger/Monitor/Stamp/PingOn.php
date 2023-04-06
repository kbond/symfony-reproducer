<?php

namespace App\Messenger\Monitor\Stamp;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class PingOn extends AttributeStamp
{
    final public function __construct(
        public readonly string $method,
        public readonly string $url,
        public readonly array $options = []
    ) {
    }
}
