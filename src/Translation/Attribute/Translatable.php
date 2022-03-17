<?php

namespace App\Translation\Attribute;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS|\Attribute::TARGET_PROPERTY)]
final class Translatable
{
    public function __construct(public ?string $alias = null)
    {
    }
}
