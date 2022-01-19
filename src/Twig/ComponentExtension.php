<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\UX\TwigComponent\ComponentFactory;
use Twig\Extension\AbstractExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[Autoconfigure(bind: ['$factory' => '@ux.twig_component.component_factory'])]
final class ComponentExtension extends AbstractExtension
{
    public function __construct(private ComponentFactory $factory)
    {
    }

    public function getTokenParsers(): array
    {
        return [
            new ComponentTokenParser($this->factory)
        ];
    }
}
