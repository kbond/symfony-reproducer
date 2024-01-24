<?php

namespace App;

use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Icon
{
    public readonly array $attributes;
    public readonly string $content;

    public function __construct(string $svg)
    {
        $crawler = (new Crawler($svg))->filter('svg');
        $attr = array_map(static fn(\DomAttr $attr) => $attr->value, iterator_to_array($crawler->getNode(0)->attributes));

        unset($attr['class'], $attr['width'], $attr['height']);

        $this->attributes = $attr;
        $this->content = $crawler->html();
    }
}
