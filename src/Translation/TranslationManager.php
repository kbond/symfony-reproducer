<?php

namespace App\Translation;

use App\Translation\Attribute\Translatable;
use App\Translation\Model\TranslatableIterator;
use App\Translation\Model\TranslatableMetadata;
use App\Translation\Model\TranslatableProxy;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TranslationManager implements CacheWarmerInterface, ResetInterface
{
    /** @var array<string,array<string,TranslatableProxy>> */
    private array $proxyCache = [];

    /** @var array<class-string,TranslatableMetadata> */
    private array $localMetadataCache;

    /**
     * @internal
     */
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private CacheInterface $metadataCache,
        private CacheInterface $translationCache,
        private string $translationClass,
    ) {
    }

    /**
     * @template T
     *
     * @param T $object
     *
     * @return TranslatableProxy<T>
     */
    public function proxyFor(object $object, string $locale, bool $forceRefresh = false): TranslatableProxy
    {
        if (!$forceRefresh && isset($this->proxyCache[$objectId = \spl_object_id($object)][$locale])) {
            return $this->proxyCache[$objectId][$locale];
        }

        $id = $this->idFor($object);
        $metadata = $this->translationMetadata()[$object::class] ?? throw new \InvalidArgumentException(\sprintf('"%s" is not a translatable object.', $object::class));

        $valueMap = $this->translationCache->get(
            \sprintf('_object_trans:%s.%s.%s', $locale, $metadata->alias, $id),
            function() use ($locale, $metadata, $id) {
                $translations = $this->managerRegistry->getRepository($this->translationClass)->findBy([
                    'locale' => $locale,
                    'object' => $metadata->alias,
                    'objectId' => $id,
                ]);

                return $metadata->createValueMap($translations);
            },
            $forceRefresh ? \INF : null
        );

        $proxy = new TranslatableProxy($object, $valueMap);

        if (isset($objectId)) {
            $this->proxyCache[$objectId][$locale] = $proxy;
        }

        return $proxy;
    }

    public function translatableObjects(): TranslatableIterator
    {
        return new TranslatableIterator(\array_keys($this->translationMetadata()), $this->managerRegistry);
    }

    public function idFor(object $object): string
    {
        if (!$om = $this->managerRegistry->getManagerForClass($object::class)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a Doctrine managed object.', $object::class));
        }

        $id = $om->getClassMetadata($object::class)->getIdentifierValues($object);

        return match(\count($id)) {
            0 => throw new \LogicException(\sprintf('"%s" is not yet persisted.', $object::class)),
            1 => $id[array_key_first($id)],
            default => throw new \LogicException('Composite IDs not supported'),
        };
    }

    /**
     * @param object|class-string $object
     */
    public function isTranslatable(object|string $object): bool
    {
        if (\is_object($object)) {
            $object = $object::class;
        }

        return \array_key_exists($object, $this->translationMetadata());
    }

    /**
     * @internal
     */
    public function warmUp(string $cacheDir): void
    {
        $this->translationMetadata();
    }

    /**
     * @internal
     */
    public function isOptional(): bool
    {
        return false;
    }

    /**
     * @internal
     */
    public function reset(): void
    {
        $this->proxyCache = [];
    }

    private static function normalizeId(array $id): string
    {
        // TODO: composite ids

        return $id[\array_key_first($id)];
    }

    /**
     * @return array<class-string,TranslatableMetadata>
     */
    private function translationMetadata(): array
    {
        return $this->localMetadataCache ??= $this->metadataCache->get(
            '_object_trans_metadata',
            function() {
                $metadata = [];

                foreach ($this->managerRegistry->getManagers() as $manager) {
                    foreach ($manager->getMetadataFactory()->getAllMetadata() as $classMetadata) {
                        if (!$attribute = Translatable::for($class = $classMetadata->getName())) {
                            continue;
                        }

                        $metadata[$class] = new TranslatableMetadata($attribute, $class);
                    }
                }

                return $metadata;
            }
        );
    }
}
