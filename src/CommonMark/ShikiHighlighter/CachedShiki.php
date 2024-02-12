<?php

namespace App\CommonMark\ShikiHighlighter;

use Spatie\ShikiPhp\Shiki;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CachedShiki extends Shiki
{
    public function __construct(private CacheInterface $cache, string $defaultTheme = 'nord')
    {
        parent::__construct($defaultTheme);
    }

    public function highlightCode(string $code, string $language, ?string $theme = null, ?array $options = []): string
    {
        return $this->cache->get(
            sprintf('shiki-%s-%s-%s-%s', $code, $language, $theme, sha1(serialize($options))),
            fn() => parent::highlightCode($code, $language, $theme, $options)
        );
    }
}
