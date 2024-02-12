<?php

namespace App\Marmalade;

use Symfony\Component\Asset\Context\ContextInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
#[AsDecorator('assets.context')]
final class AssetContextDecorator implements ContextInterface
{
    private string $basePath;

    public function __construct(private ContextInterface $inner)
    {
    }

    public function setBasePath(string $value): void
    {
        $this->basePath = $value;
    }

    public function getBasePath(): string
    {
        return $this->basePath ?? $this->inner->getBasePath();
    }

    public function isSecure(): bool
    {
        return $this->inner->getBasePath();
    }
}
