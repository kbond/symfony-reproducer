<?php

namespace App\Messenger\Monitor\Stamp;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Tag extends AttributeStamp
{
    public readonly array $values;

    public function __construct(string ...$tags)
    {
        $this->values = $tags;
    }
}
