<?php

namespace App\Translation;

use App\Translation\Attribute\Translatable;
use App\Translation\Model\Translation;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TranslationManager
{
    public function __construct(private ManagerRegistry $managerRegistry)
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

        [$alias, $propertyMap] = $this->translationMetadata($object);

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

    private static function normalizeId(array $id): string
    {
        // TODO: composite ids

        return $id[\array_key_first($id)];
    }

    /**
     * @return array{0:string, 1 array<string,string>}
     */
    private function translationMetadata(object $object): array
    {
        // TODO: cache/warmup
        $ref = new \ReflectionClass($object);

        if (!$attribute = $ref->getAttributes(Translatable::class)[0] ?? null) {
            throw new \InvalidArgumentException(\sprintf('"%s" is not a translatable object.', $object::class));
        }

        $alias = $attribute->newInstance()->alias ?? $object::class;
        $propertyMap = [];

        foreach ($ref->getProperties() as $property) {
            if (!$attribute = $property->getAttributes(Translatable::class)[0] ?? null) {
                continue;
            }

            $propertyMap[$attribute->newInstance()->alias ?? $property->name] = \strtoupper($property->name);
        }

        return [$alias, $propertyMap];
    }
}
