<?php

namespace App\Routing\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ObjectUrlGeneratorExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('object_path', [ObjectUrlGeneratorRuntime::class, 'generatePath']),
            new TwigFunction('object_url', [ObjectUrlGeneratorRuntime::class, 'generateUrl']),
        ];
    }
}
