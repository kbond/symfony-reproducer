<?php

namespace App\Translation\Twig;

use App\Translation\TranslatableProxy;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TranslateExtension extends AbstractExtension
{
    public function __construct(private string $defaultLocale)
    {
    }

    public function getFilters(): array
    {
        // TODO: twig runtime
        return [
            new TwigFilter('translate', [$this, 'translate'])
        ];
    }

    /**
     * @template T
     *
     * @param T $object
     *
     * @return T|TranslatableProxy<T>
     */
    public function translate(object $object, ?string $locale = null): object
    {
        $locale = $locale ?? \Locale::getDefault();

        if ($locale === $this->defaultLocale) {
            return $object;
        }

        return new TranslatableProxy($object);
    }
}
