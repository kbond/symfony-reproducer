---
title: Styles and Formatting
description: What some of the basic type styles will look like in this starter template
date: 2019-01-20
---

This starter template is based on the [Start Bootstrap Clean Blog](https://startbootstrap.com/template-overviews/clean-blog/) theme, which is built on top of  [Bootstrap 4](https://getbootstrap.com/), the world's most popular front-end component library.

---

## Typography Styles

Here’s a quick preview of what some of the basic type styles will look like in this starter template:

# h1 Heading. h1 Heading. h1 Heading. h1 Heading. h1 Heading.
## h2 Heading
### h3 Heading
#### h4 Heading
##### h5 Heading
###### h6 Heading

* here
* is
* an
* unordered
    1. nested
    2. inside
        * deeply
        * nested
    3. another
* list

---

1. here
2. is
3. an
4. ordered
5. list

<div class="border-2 border-orange-500 p-4 my-4 border-dotted font-bold">Custom HTML</div>

The quick brown fox jumps over the lazy dog (plain paragraph)

<s>The quick brown fox jumps over the lazy dog (strike-through)</s>

<u>The quick brown fox jumps over the lazy dog (underline)</u>

_The quick brown fox jumps over the lazy dog (italic)_

**The quick brown fox jumps over the lazy dog (bold)**

`The quick brown fox jumps over the lazy dog (code)`

Some `code in a` paragraph (code). 

<small>The quick brown fox jumps over the lazy dog (small)</small>

> The quick brown fox jumps over the lazy dog (blockquote)

[The quick brown fox jumps over the lazy dog (link)](#)

```php
class Foo extends bar
{
    public function fooBar()
    {
        //
    }
}
```

A really long code block:

```php
class Foo extends bar
{
    public function fooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBarfooBar()
    {
        //
    }
}
```

Full code example:

```php
<?php

use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;
use TightenCo\Jigsaw\Jigsaw;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class GenerateFeed
{
    public function handle(Jigsaw $jigsaw): void
    {
        $config = $jigsaw->getConfig();

        if (!$config['baseUrl']) {
            echo("\nTo generate a rss.xml file, please specify a 'baseUrl' in config.php.\n\n");

            return;
        }

        $feed = new Feed();
        $channel = new Channel();

        $channel
            ->title($config['siteName'])
            ->description($config['siteDescription'])
            ->url($config['baseUrl'])
            ->feedUrl(rtrim($config['baseUrl'], '/') . '/feed.xml')
            ->language('en-US')
            ->copyright('Copyright © '. $config['siteName'] . ' ' . (new \DateTime())->format('Y'))
            ->pubDate((new \DateTime())->getTimestamp())
            ->lastBuildDate((new \DateTime())->getTimestamp())
            ->appendTo($feed)
        ;

        $jigsaw->getCollection('posts')->each(function ($post) use ($channel, $config) {
            // Blog item
            $item = new Item();
            $item
                ->title($post->title)
                ->description($post->description())
                ->contentEncoded($post)
                ->url($post->getUrl())
                ->pubDate((new \DateTime('@'.$post->date))->getTimestamp())
                ->guid($post->getUrl(), true)
                ->preferCdata(true) // By this, title and description become CDATA wrapped HTML.
                ->appendTo($channel);
        });

        // Using $jigsaw->writeOutputFile() would create empty folders see https://github.com/tightenco/jigsaw/issues/322
        // $jigsaw->writeOutputFile('feed.xml', $feed->render());

        file_put_contents($jigsaw->getDestinationPath() . '/feed.xml', $feed->render());
    }
}
```
