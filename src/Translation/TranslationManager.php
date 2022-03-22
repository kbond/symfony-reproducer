<?php

namespace App\Translation;

use App\Translation\Attribute\Translatable;
use App\Translation\Model\Translation;
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

    /** @var array<class-string,array{0:string, 1:array<string,string>}> */
    private array $localMetadataCache;

    /**
     * @internal
     */
    public function __construct(
        private ManagerRegistry $managerRegistry,
        private CacheInterface $metadataCache,
        private CacheInterface $translationCache,
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

        if (!$om = $this->managerRegistry->getManagerForClass($object::class)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a managed object.', $object::class));
        }

        [$alias, $propertyMap] = $this->translationMetadata()[$object::class] ?? throw new \InvalidArgumentException(\sprintf('"%s" is not a translatable object.', $object::class));

        $id = self::normalizeId($om->getClassMetadata($object::class)->getIdentifierValues($object));

        $values = $this->translationCache->get(
            \sprintf('_object_trans:%s.%s.%s', $locale, $alias, $id),
            function() use ($locale, $alias, $id, $propertyMap) {
                // TODO: warmup concept
                $translations = $this->managerRegistry->getRepository(Translation::class)->findBy([
                    'locale' => $locale,
                    'object' => $alias,
                    'objectId' => $id,
                ]);

                $valueMap = [];

                foreach ($translations as $translation) {
                    if (isset($propertyMap[$translation->field])) {
                        $valueMap[$propertyMap[$translation->field]] = $translation->value;
                    }
                }

                return $valueMap;
            },
            $forceRefresh ? \INF : null
        );

        $proxy = new TranslatableProxy($object, $values);

        if (isset($objectId)) {
            $this->proxyCache[$objectId][$locale] = $proxy;
        }

        return $proxy;
    }

    public function translatableObjects(): TranslatableObjectIterator
    {
        return new TranslatableObjectIterator(\array_keys($this->translationMetadata()), $this->managerRegistry);
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
     * @return array<class-string,array{0:string, 1:array<string,string>}>
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

                        $alias = $attribute->alias ?? $class;
                        $propertyMap = [];

                        foreach (Translatable::propertiesFor($class) as $property => $attribute) {
                            $propertyMap[$attribute->alias ?? $property->name] = \strtoupper($property->name);
                        }

                        $metadata[$class] = [$alias, $propertyMap];
                    }
                }

                return $metadata;
            }
        );
    }
}
