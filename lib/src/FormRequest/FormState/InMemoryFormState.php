<?php

namespace Zenstruck\FormRequest\FormState;

use Zenstruck\FormRequest\FormState;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class InMemoryFormState extends FormState
{
    /** @var array<string, mixed> */
    private array $data = [];

    /** @var array<string, string[]> */
    private array $errors = [];

    public function data(): array
    {
        return $this->data;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function set(string $field, mixed $value): self
    {
        $this->data[$field] = $value;

        return $this;
    }

    public function addError(string $field, string $message): self
    {
        $this->errors[$field][] = $message;

        return $this;
    }

    public function addGlobalError(string $message): self
    {
        return $this->addError(self::GLOBAL_ERROR_KEY, $message);
    }
}
