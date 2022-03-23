<?php

namespace App\Translation\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @implements \IteratorAggregate<string,Translation>
 */
final class TranslationCollection implements \Countable, \IteratorAggregate
{
    /**
     * @param array<string,Translation> $translations
     */
    public function __construct(private array $translations)
    {
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->translations);
    }

    public function count(): int
    {
        return \count($this->translations);
    }
}
