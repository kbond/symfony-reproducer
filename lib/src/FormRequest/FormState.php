<?php

namespace Zenstruck\FormRequest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class FormState
{
    protected const GLOBAL_ERROR_KEY = '_global';

    final public function __get(string $field): mixed
    {
        return $this->get($field);
    }

    final public function __isset(string $field): bool
    {
        return $this->has($field);
    }

    final public function get(string $field, mixed $default = null): mixed
    {
        return $this->data()[$field] ?? $default;
    }

    final public function has(string $field): bool
    {
        return \array_key_exists($field, $this->data());
    }

    /**
     * @return string[]
     */
    final public function errorsFor(string $field): array
    {
        return $this->errors()[$field] ?? [];
    }

    /**
     * @return string[]
     */
    final public function globalErrors(): array
    {
        return $this->errorsFor(self::GLOBAL_ERROR_KEY);
    }

    /**
     * @param string|null $field Specific field to check
     *
     * @return bool If $field passed, true if submitted and field has to errors, false otherwise
     *              If no $field passed, true if the entire form is valid, false otherwise
     */
    final public function isValid(?string $field = null): bool
    {
        if ($field) {
            return $this->isSubmitted() && !$this->errorsFor($field);
        }

        return 0 === \count($this->errors());
    }

    final public function isSubmitted(): bool
    {
        return \count($this->data()) > 0;
    }

    final public function isSubmittedAndValid(): bool
    {
        return $this->isValid() && $this->isSubmitted();
    }

    /**
     * @return array<string, string[]>
     */
    abstract public function errors(): array;

    /**
     * @return array<string, mixed>
     */
    abstract public function data(): array;
}
