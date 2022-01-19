<?php

namespace App\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('my_component')]
final class MyComponent
{
    public $prop;
}
