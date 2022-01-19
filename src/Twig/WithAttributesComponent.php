<?php

namespace App\Twig;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\HasAttributesTrait;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsTwigComponent('with_attributes')]
final class WithAttributesComponent
{
    use HasAttributesTrait;
}
