<?php

namespace App\Twig\Components;

use App\Icon\IconRegistry;
use App\Icon\IconStack;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Icon
{
    private string $name;
    private bool $defer;
    private string $tag = 'svg';

    public function __construct(
        private IconRegistry $registry, // todo lazy
        private IconStack $stack, // todo lazy
    ) {
    }

    public function mount(string $name, bool $defer = false, bool $symbol = false): void
    {
        $this->name = $name;

        if ($this->defer = $defer) {
            $this->stack->push($name);
        }

        if ($symbol) {
            $this->tag = 'symbol';
        }
    }

    public function tag(): string
    {
        return $this->tag;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function icon(): string
    {
        return $this->registry->get($this->name);
    }

    public function isDeferred(): bool
    {
        return $this->defer;
    }
}
