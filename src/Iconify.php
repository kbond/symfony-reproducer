<?php

namespace App;

use App\Iconify\SetMetadata;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Iconify
{
    public function __construct(
        private HttpClientInterface $client
    ) {
    }

    public function set(string $name): SetMetadata
    {
        return new SetMetadata($this->client
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
}
