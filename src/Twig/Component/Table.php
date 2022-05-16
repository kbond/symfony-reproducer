<?php

namespace App\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('table')]
final class Table
{
    public ?string $caption = null;
    public array $headers;
    public array $data;
}
