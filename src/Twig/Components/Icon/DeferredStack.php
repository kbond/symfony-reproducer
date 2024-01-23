<?php

namespace App\Twig\Components\Icon;

use App\Icon\IconStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class DeferredStack implements \IteratorAggregate
{
    public function __construct(private IconStack $stack)
    {
    }

    public function getIterator(): \Traversable
    {
        return $this->stack;
    }
}
