<?php

namespace Zenstruck\FormRequest;

use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Zenstruck\FormRequest\Exception\ValidationFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class Form
{
    private const GLOBAL_ERROR_KEY = '_global';

    /** @var array<string, string[]> */
    private array $errors = [];

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(private array $data = [])
    {
    }

    final public static function denormalizationError(array $data, NotNormalizableValueException $exception): self
    {
        if (!$type = $exception->getCurrentType()) {
            return (new self($data))->addGlobalError('Could not process given data.');
        }

        $type = \class_exists($type) ? 'object' : $type;
        $path = $exception->getPath();

        if (!$path) {
            return (new self($data))->addGlobalError(\sprintf('Could not process "%s".', $type));
        }

        return (new self($data))->addError(\explode('[', $path)[0], \sprintf('Type "%s" is invalid.', $type));
    }

    final public function data(): array
    {
        return $this->data;
    }

    final public function errors(): array
    {
        return $this->errors;
    }

    final public function set(string $field, mixed $value): static
    {
        $this->data[$field] = $value;

        return $this;
    }

    final public function addError(string $field, string $message): static
    {
        $this->errors[$field][] = $message;

        return $this;
    }

    final public function addGlobalError(string $message): static
    {
        return $this->addError(self::GLOBAL_ERROR_KEY, $message);
    }

    final public function get(string $field, mixed $default = null): mixed
    {
        return $this->data[$field] ?? $default;
    }

    final public function has(string $field): bool
    {
        return \array_key_exists($field, $this->data);
    }

    /**
     * @return string[]
     */
    final public function errorsFor(string $field): array
    {
        return $this->errors[$field] ?? [];
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

        return 0 === \count($this->errors);
    }

    final public function isSubmitted(): bool
    {
        return \count($this->data) > 0;
    }

    final public function isSubmittedAndValid(): bool
    {
        return $this->isValid() && $this->isSubmitted();
    }

    final public function throwIfInvalid(): static
    {
        if (!$this->isValid()) {
            throw new ValidationFailed($this);
        }

        return $this;
    }
}
