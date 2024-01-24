<?php

namespace App\Icon;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class IconKitManager
{
    private const KITS = [
        'heroicons' => [
            'source' => 'tailwindlabs/heroicons',
            'variants' => [
                '16/solid' => 'src/16/solid',
                '20/solid' => 'src/20/solid',
                '24/solid' => 'src/24/solid',
                '24/outline' => 'src/24/outline',
            ],
        ],
        'fontawesome' => [
            'source' => 'FortAwesome/Font-Awesome',
            'variants' => [
                'brands' => 'svgs/brands',
                'regular' => 'svgs/regular',
                'solid' => 'svgs/solid',
            ],
        ],
        'twbs' => [
            'source' => 'twbs/icons',
            'variants' => [
                '' => 'icons',
            ],
        ],
    ];

    public function __construct(
        #[Autowire('%kernel.project_dir%/templates/icons')]
        private string $iconDir,

        #[Autowire('%kernel.project_dir%/var')]
        private string $tempDir,

        private HttpClientInterface $http,
    ) {
    }

    public function availableKits(): array
    {
        return array_keys(self::KITS);
    }

    public function require(string $name): void
    {
        if (!isset(self::KITS[$name])) {
            throw new \InvalidArgumentException(sprintf('The icon kit "%s" does not exist. Available kits: "%s".', $name, implode('", "', $this->availableKits())));
        }

        $kit = self::KITS[$name];
        $zipUrl = $this->http
            ->request('GET', sprintf('https://api.github.com/repos/%s/releases/latest', $kit['source']))
            ->toArray()['zipball_url'] ?? throw new \RuntimeException(sprintf('Could not find latest release for "%s".', $kit['source']))
        ;

        $fs = new Filesystem();
        $tempFile = sprintf('%s/ux-icon-kit.zip', $this->tempDir);
        $tempDir = sprintf('%s/ux-icon-kit', $this->tempDir);

        $fs->remove([$tempFile, $tempDir]);

        $stream = $this->http->request('GET', $zipUrl)->toStream();

        $fs->dumpFile($tempFile, $stream);

        fclose($stream);

        $zip = new \ZipArchive();
        $zip->open($tempFile);
        $zip->extractTo($tempDir);
        $zip->close();

        $kitDir = sprintf('%s/vendor/%s', $this->iconDir, $name);

        $fs->remove($kitDir);

        foreach (self::KITS[$name]['variants'] as $variant => $path) {
            $finder = (new Finder())->in($tempDir)->path($path)->files()->name('*.svg');

            foreach ($finder as $file) {
                $destFilename = sprintf('%s/%s/%s', $kitDir, $variant, $file->getFilename());

                $fs->copy($file, $destFilename);
            }
        }

        $fs->remove([$tempFile, $tempDir]);
    }
}
