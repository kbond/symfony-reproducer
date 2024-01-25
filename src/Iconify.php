<?php

namespace App;

use App\Iconify\IconSet;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Iconify
{
    public function __construct(
        private HttpClientInterface $client,
        private CacheInterface $cache,
    ) {
    }

    public function fetchSet(string $name): IconSet
    {
        return new IconSet($this->client
            ->request('GET', sprintf('https://api.iconify.design/collection?prefix=%s', $name))
            ->toArray()
        );
    }

    public function svg(string $prefix, string $name): string
    {
        return $this->client
            ->request('GET', sprintf('https://api.iconify.design/%s/%s.svg', $prefix, $name))
            ->getContent()
        ;
    }

    /**
     * @return string[]
     */
    public function fetchSetNames(): array
    {
        return $this->cache->get('iconify_set_names', function (ItemInterface $item) {
            $item->expiresAfter(86400);

            return array_keys($this->client
                ->request('GET', 'https://api.iconify.design/collections')
                ->toArray()
            );
        });
    }
}
