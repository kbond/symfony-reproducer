<?php

namespace App\Translation\Attribute;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS|\Attribute::TARGET_PROPERTY)]
final class Translatable
{
    public function __construct(public ?string $alias = null)
    {
    }

    /**
     * @param class-string $class
     */
    public static function for(string $class): ?self
    {
        if (!$attribute = (new \ReflectionClass($class))->getAttributes(self::class)[0] ?? null) {
            return null;
        }

        return $attribute->newInstance();
    }

    /**
     * @param class-string $class
     *
     * @return \Traversable<\ReflectionProperty,self>
     */
    public static function propertiesFor(string $class): \Traversable
    {
        foreach ((new \ReflectionClass($class))->getProperties() as $property) {
            if (!$attribute = $property->getAttributes(self::class)[0] ?? null) {
                continue;
            }

            yield $property => $attribute->newInstance();
        }
    }
}
