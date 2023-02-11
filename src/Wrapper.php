<?php

namespace App;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: Implementation::class)]
final class Wrapper implements Contract
{
    public function __construct(private Contract $inner)
    {
    }
}
