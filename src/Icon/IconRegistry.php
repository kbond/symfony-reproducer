<?php

namespace App\Icon;

use App\Icon;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class IconRegistry
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/templates/icons')]
        private string $iconDir,
        private CacheInterface $cache,
    ) {
    }

    public function get(string $name, bool $refresh = false): Icon
    {
        $name = str_replace('@', 'vendor/', $name);

        return $this->cache->get(
            $this->buildCacheKey($name),
            function(ItemInterface $item) use ($name) {
                if (!file_exists($filename = $this->buildFilename($name))) {
                    throw new \RuntimeException(sprintf('The icon "%s" does not exist.', $filename));
                }

                if ($this->cache instanceof TagAwareCacheInterface) {
                    $item->tag('ux-icon');
                }

                return new Icon(file_get_contents($filename) ?: throw new \RuntimeException(sprintf('The icon "%s" could not be read.', $filename)));
            },
            beta: $refresh ? INF : null,
        );
    }

    public function add(string $name, string $svg): void
    {
        $this->cache->delete($this->buildCacheKey($name));

        (new Filesystem())->dumpFile($this->buildFilename($name), $svg);
    }

    /**
     * @return string[]
     */
    public function names(): \Traversable
    {
        foreach (Finder::create()->in($this->iconDir)->files()->name('*.svg') as $file) {
            yield str_replace(['.svg', 'vendor/'], ['', '@'], $file->getRelativePathname());
        }
    }

    private function buildFilename(string $name): string
    {
        return sprintf('%s/%s.svg', $this->iconDir, str_replace(':', '/', $name));
    }

    private function buildCacheKey(string $name): string
    {
        return sprintf('ux-icon-%s', $name);
    }
}
