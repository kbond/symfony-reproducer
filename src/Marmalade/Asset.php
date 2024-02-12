<?php

namespace App\Marmalade;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Asset
{
    public function __construct(public readonly string $path, public readonly \SplFileInfo|string $contents)
    {
    }
}
