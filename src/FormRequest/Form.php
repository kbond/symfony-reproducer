<?php

namespace App\FormRequest;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Form
{
    private const GLOBAL_ERROR_KEY = '_global';

    /** @var array<string,string[]> */
    private array $errors = [];

    /**
     * @param array<string,mixed> $data
     */
    public function __construct(private array $data = [], private ?string $csrfTokenId = null)
    {
    }

    public function csrfTokenId(): ?string
    {
        return $this->csrfTokenId;
    }

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

    public function get(string $field, mixed $default = null): mixed
    {
        return $this->has($field) ? $this->data[$field] : $default;
    }

    public function has(string $field): bool
    {
        return \array_key_exists($field, $this->data);
    }

    /**
     * @return string[]
     */
    public function errorsFor(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    /**
     * @return string[]
     */
    public function globalErrors(): array
    {
        return $this->errorsFor(self::GLOBAL_ERROR_KEY);
    }

    /**
     * @param string|null $field Specific field to check
     *
     * @return bool If $field passed, true if submitted and field has to errors, false otherwise
     *              If no $field passed, true if the entire form is valid, false otherwise
     */
    public function isValid(?string $field = null): bool
    {
        if ($field) {
            return $this->data && !$this->errorsFor($field);
        }

        return 0 === \count($this->errors);
    }

    public function isSubmitted(): bool
    {
        return \count($this->data) > 0;
    }

    public function isSubmittedAndValid(): bool
    {
        return $this->isValid() && $this->isSubmitted();
    }
}
