<?php

namespace App\Translation\Model;

use App\Translation\Attribute\Translatable;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class TranslatableMetadata
{
    /** @readonly */
    public string $alias;

    /**
     * @readonly
     * @var array<string,string>
     */
    public array $propertyMap = [];

    /**
     * @param class-string $class
     */
    public function __construct(Translatable $attribute, string $class)
    {
        $this->alias = $attribute->alias ?? $class;

        foreach (Translatable::propertiesFor($class) as $property => $propertyAttribute) {
            $this->propertyMap[$propertyAttribute->alias ?? $property->name] = $property->name;
        }
    }

    /**
     * @param Translation[] $translations
     */
    public function createValueMap(iterable $translations): TranslatableValueMap
    {
        $values = [];

        foreach ($translations as $translation) {
            if (isset($this->propertyMap[$translation->field])) {
                $values[$this->propertyMap[$translation->field]] = $translation->value;
            }
        }

        return new TranslatableValueMap($values);
    }
}
