<?php

namespace App\Icon;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class IconCacheWarmer implements CacheWarmerInterface
{
    public function __construct(private IconRegistry $registry)
    {
    }

    public function isOptional(): bool
    {
        return true;
    }

    public function warmUp(string $cacheDir): void
    {
        foreach ($this->registry->names() as $name) {
            $this->registry->get($name, refresh: true);
        }
    }
}
