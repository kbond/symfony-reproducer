<?php

namespace App\Translation;

use App\Translation\Attribute\Translatable;
use App\Translation\Model\Translation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TranslationManager implements CacheWarmerInterface
{
    public function __construct(private ManagerRegistry $managerRegistry, private CacheInterface $metadataCache)
    {
    }

    /**
     * @template T
     *
     * @param T $object
     *
     * @return TranslatableProxy<T>
     */
    public function proxyFor(object $object, string $locale): TranslatableProxy
    {
        if (!$om = $this->managerRegistry->getManagerForClass($object::class)) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a managed object.', $object::class));
        }

        [$alias, $propertyMap] = $this->translationMetadata($object::class);

        $id = self::normalizeId($om->getClassMetadata($object::class)->getIdentifierValues($object));

        // TODO: caching (warmup option?)
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

        return new TranslatableProxy($object, $valueMap);
    }

    public function warmUp(string $cacheDir): void
    {
        foreach ($this->managerRegistry->getManagers() as $manager) {
            foreach ($manager->getMetadataFactory()->getAllMetadata() as $metadata) {
                if (Translatable::for($metadata->getName())) {
                    $this->translationMetadata($metadata->getName());
                }
            }
        }
    }

    public function isOptional(): bool
    {
        return false;
    }

    private static function normalizeId(array $id): string
    {
        // TODO: composite ids

        return $id[\array_key_first($id)];
    }

    /**
     * @param class-string $class
     *
     * @return array{0:string, 1 array<string,string>}
     */
    private function translationMetadata(string $class): array
    {
        return $this->metadataCache->get(
            '_object_trans_metadata:'.$class,
            function() use ($class) {
                if (!$attribute = Translatable::for($class)) {
                    throw new \InvalidArgumentException(\sprintf('"%s" is not a translatable object.', $class));
                }

                $alias = $attribute->alias ?? $class;
                $propertyMap = [];

                foreach (Translatable::propertiesFor($class) as $property => $attribute) {
                    $propertyMap[$attribute->alias ?? $property->name] = \strtoupper($property->name);
                }

                return [$alias, $propertyMap];
            },
        );
    }
}
