<?php

namespace App\Icon;

use Symfony\Contracts\Service\ResetInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class IconStack implements ResetInterface, \IteratorAggregate
{
    /** @var array<string,true> */
    private array $icons = [];

    public function push(string $name): void
    {
        if (isset($this->icons[$name])) {
            return;
        }

        $this->icons[$name] = true;
    }

    public function getIterator(): \Traversable
    {
        try {
            return new \ArrayIterator(array_keys($this->icons));
        } finally {
            $this->reset();
        }
    }

    public function reset(): void
    {
        $this->icons = [];
    }
}
