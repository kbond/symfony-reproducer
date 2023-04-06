<?php

namespace App\Messenger\Monitor\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Tag implements StampInterface
{
    public readonly array $tags;

    public function __construct(string ...$tags)
    {
        $this->tags = $tags;
    }
}
