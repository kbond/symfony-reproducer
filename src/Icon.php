<?php

namespace App;

use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Icon
{
    public readonly string $content;

    public function __construct(string $svg)
    {
        $crawler = (new Crawler($svg))->filter('svg');

        $this->content = $crawler->html();
    }
}
