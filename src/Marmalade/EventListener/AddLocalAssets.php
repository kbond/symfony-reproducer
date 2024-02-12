<?php

namespace App\Marmalade\EventListener;

use App\Marmalade\Event\AddAssets;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Finder\Finder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsEventListener]
final class AddLocalAssets
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/templates/marmalade/pages')]
        private string $dir,
        private array $exclude = ['*.md', '*.twig'],
    ) {
    }

    public function __invoke(AddAssets $event): void
    {
        $event->addFrom((new Finder())->in($this->dir)->files()->notName($this->exclude));
    }
}
