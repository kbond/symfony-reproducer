<?php

namespace App\Marmalade;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class PageCollection implements \IteratorAggregate, \Countable
{
    public const HOMEPAGE = 'index';

    private array $filtered;

    /**
     * @param array<string,Page> $pages
     */
    public function __construct(private array $pages)
    {
        $this->filtered = $this->pages;
    }

    public function get(string $path): Page
    {
        return $this->pages[$path] ?? throw new \InvalidArgumentException(sprintf('Page "%s" not found.', $path));
    }

    public function excludeHomepage(): self
    {
        $clone = clone $this;
        unset($clone->filtered[self::HOMEPAGE]);

        return $clone;
    }

    public function in(string $prefix): self
    {
        $clone = clone $this;
        $clone->filtered = array_filter($clone->filtered, fn (Page $page) => str_starts_with($page->path, $prefix));

        return $clone;
    }

    public function sortByAsc(string $key): self
    {
        $clone = clone $this;
        usort($clone->filtered, fn (Page $a, Page $b) => $a[$key] <=> $b[$key]);

        return $clone;
    }

    public function sortByDesc(string $key): self
    {
        $clone = clone $this;
        usort($clone->filtered, fn (Page $a, Page $b) => $b[$key] <=> $a[$key]);

        return $clone;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->filtered);
    }

    public function count(): int
    {
        return count($this->filtered);
    }
}
