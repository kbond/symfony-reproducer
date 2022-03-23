<?php

namespace App\Translation\Model;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @template T
 *
 * @mixin T
 */
final class TranslatableProxy
{
    public function __construct(private object $translatable, private TranslatableValueMap $valueMap)
    {
    }

    public function __call(string $name, array $arguments): mixed
    {
        if ($translatedValue = $this->valueMap->get($name)) {
            return $translatedValue;
        }

        if (isset($this->translatable->$name)) {
            // try property
            return $this->translatable->$name;
        }

        if ($this->translatable instanceof \ArrayAccess && isset($this->translatable[$name])) {
            return $this->translatable[$name];
        }

        return $this->translatable->{$this->normalizeMethod($name)}(...$arguments);
    }

    /**
     * @return T
     */
    public function translatableObject(): object
    {
        return $this->translatable;
    }

    /**
     * @return array<string,mixed>
     */
    public function translatableValues(): array
    {
        return $this->valueMap->values();
    }

    private function normalizeMethod(string $name): string
    {
        if (method_exists($this->translatable, $name)) {
            return $name;
        }

        foreach (['get', 'is', 'has'] as $prefix) {
            if (method_exists($this->translatable, $method = sprintf('%s%s', $prefix, ucfirst($name)))) {
                return $method;
            }
        }

        throw new \InvalidArgumentException(sprintf('Object "%s" does not have a "%s" method.', $this->translatable::class, $name));
    }
}
