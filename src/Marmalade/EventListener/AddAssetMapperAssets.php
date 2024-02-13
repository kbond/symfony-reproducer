<?php

namespace App\Marmalade\EventListener;

use App\Marmalade\Event\BuildAssets;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
#[AsEventListener]
final class AddAssetMapperAssets
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/assets')]
        private string $assetsDir,

        #[Autowire('%kernel.debug%')]
        private bool $debug,
    ) {
    }

    public function __invoke(BuildAssets $event): void
    {
        if ($this->debug) {
            return;
        }

        $file = new \SplFileInfo($this->assetsDir);

        if (!$file->isDir()) {
            throw new \LogicException(sprintf('The assets directory "%s" does not exist. Run "bin/console asset-map:compile --env=prod" and try again.', $this->assetsDir));
        }

        $event->addAsset('/assets', $file);
    }
}
