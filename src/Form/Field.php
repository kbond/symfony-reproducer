<?php

namespace App\Form;

use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Field
{
    /**
     * @param class-string<FormTypeInterface> $type
     */
    public function __construct(
        public string $name,
        public string $type,
        public array $options = [],
    ) {
    }

    /**
     * @return iterable<array{Field, \ReflectionProperty}>
     */
    public static function fieldsFrom(object $object): iterable
    {
        foreach ((new \ReflectionClass($object))->getProperties() as $property) {
            if (!$field = $property->getAttributes(self::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null) {
                continue;
            }

            yield [$field->newInstance(), $property];
        }
    }
}
