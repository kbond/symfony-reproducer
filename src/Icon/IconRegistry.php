<?php

namespace App\Icon;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
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

    public function get(string $name, bool $refresh = false): string
    {
        return $this->cache->get(
            $this->buildCacheKey($name),
            function(ItemInterface $item) use ($name) {
                if (!file_exists($filename = $this->buildFilename($name))) {
                    throw new \RuntimeException(sprintf('The icon "%s" does not exist.', $filename));
                }

                if ($this->cache instanceof TagAwareCacheInterface) {
                    $item->tag('ux-icon');
                }

                $svg = file_get_contents($filename) ?: throw new \RuntimeException(sprintf('The icon "%s" could not be read.', $filename));

                return (new Crawler($svg))->filter('svg')->html();
            },
            beta: $refresh ? INF : null,
        );
    }

    public function add(string $name, string $svg): void
    {
        $this->cache->delete($this->buildCacheKey($name));

        (new Filesystem())->dumpFile($this->buildFilename($name), $svg);
    }

    public function addSet(string $name, array $set): void
    {
        $set['icons'] = array_map(
            fn(array $icon) => $icon['body'],
            $set['icons'] ?? [],
        );

        $file = sprintf('%s/%s.php', $this->iconDir, $name);

        (new Filesystem())->dumpFile($file, sprintf('<?php return %s;', var_export($set, true)));
    }

    /**
     * Return all registered icon names.
     *
     * @return string[]
     */
    public function names(): array
    {
        return array_map(
            fn(SplFileInfo $file) => str_replace(['/', '.svg'], [':', ''], $file->getRelativePathname()),
            iterator_to_array(Finder::create()->in($this->iconDir)->files()->name('*.svg')->sortByName())
        );
    }

    /**
     * Return all registered sets with their icon names.
     *
     * @return array<string,string[]>
     */
    public function sets(): array
    {
        $all = $this->names();
        $sets = [];

        foreach ($all as $icon) {
            $parts = explode(':', $icon, 2);
            $set = isset($parts[1]) ? $parts[0] : '';

            $sets[$set][] = $icon;
        }

        return $sets;
    }

    private function buildFilename(string $name): string
    {
        return sprintf('%s/%s.svg', $this->iconDir, str_replace([':'], ['/'], $name));
    }

    private function buildCacheKey(string $name): string
    {
        return sprintf('ux-icon-%s', str_replace([':'], ['-'], $name));
    }
}
