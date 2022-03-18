<?php

namespace App\Translation;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @template T
 *
 * @mixin T
 */
final class TranslatableProxy
{
    public function __construct(private object $translatable, private array $translations)
    {
    }

    public function __call(string $name, array $arguments): mixed
    {
        if (isset($this->translations[$normalized = self::normalizeName($name)])) {
            return $this->translations[$normalized];
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

    private static function normalizeName(string $name): string
    {
        $name = \strtoupper($name);

        if (\str_starts_with($name, 'GET')) {
            $name = \substr($name, 3);
        }

        return $name;
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
