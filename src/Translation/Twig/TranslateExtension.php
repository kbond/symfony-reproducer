<?php

namespace App\Translation\Twig;

use App\Translation\Model\TranslatableProxy;
use App\Translation\TranslationManager;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TranslateExtension extends AbstractExtension implements ServiceSubscriberInterface, LocaleAwareInterface
{
    private ?string $currentLocale = null;

    public function __construct(private string $defaultLocale, private ContainerInterface $container)
    {
    }

    public static function getSubscribedServices(): array
    {
        return [TranslationManager::class];
    }

    public function setLocale(string $locale): void
    {
        $this->currentLocale = $locale;
    }

    public function getLocale(): string
    {
        return $this->currentLocale ?? $this->defaultLocale;
    }

    public function getFilters(): array
    {
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
        $locale = $locale ?? $this->getLocale();

        if ($locale === $this->defaultLocale) {
            return $object;
        }

        return $this->container->get(TranslationManager::class)->proxyFor($object, $locale);
    }
}
