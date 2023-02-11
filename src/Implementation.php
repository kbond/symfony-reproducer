<?php

namespace App;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: Contract::class)]
final class Implementation implements Contract
{
}
