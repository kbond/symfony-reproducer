<?php

namespace App\Marmalade\Event;

use App\Marmalade\Asset;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class BuildAssets extends Event
{
    /** @var Asset[] */
    private array $assets = [];

    public function addAsset(string $path, \SplFileInfo|string $contents): void
    {
        $this->assets[] = new Asset($path, $contents);
    }

    public function addFrom(Finder|SplFileInfo|Asset $asset): void
    {
        if ($asset instanceof Asset) {
            $this->assets[] = $asset;

            return;
        }

        if ($asset instanceof Finder) {
            foreach ($asset as $file) {
                $this->addAsset($file->getRelativePathname(), $file);
            }

            return;
        }

        $this->addAsset($asset->getRelativePathname(), $asset);
    }

    /**
     * @return Asset[]
     */
    public function assets(): array
    {
        return $this->assets;
    }
}
