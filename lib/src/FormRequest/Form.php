<?php

namespace Zenstruck\FormRequest;

use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
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

    final public static function missingConstructorArguments(array $data, MissingConstructorArgumentsException $exception): self
    {
        $form = new Form($data);

        if (!$fields = $exception->getMissingConstructorArguments()) {
            return $form->addGlobalError('Could not process given data.');
        }

        foreach ($fields as $field) {
            $form->addError($field, 'Required.');
        }

        return $form;
    }

    final public static function denormalizationError(array $data, NotNormalizableValueException $exception): self
    {
        $form = new self($data);

        if (!$type = $exception->getCurrentType()) {
            return $form->addGlobalError('Could not process given data.');
        }

        // ensure class name not leaked
        $type = \class_exists($type) ? 'object' : $type;
        $path = $exception->getPath();

        if (!$path) {
            return $form->addGlobalError(\sprintf('Could not process "%s".', $type));
        }

        // ensure class names are not leaked
        $expected = \array_map(fn(string $t) => \class_exists($t) ? 'object' : $t, $exception->getExpectedTypes() ?? []);
        $message = \sprintf('Type "%s" is invalid.', $type);

        if ($expected) {
            $message .= \sprintf(' Valid types: "%s"', \implode('|', $expected));
        }

        return $form->addError($path, $message);
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
        // TODO: can this method be removed?
        return \count($this->data) > 0;
    }

    final public function isSubmittedAndValid(): bool
    {
        // TODO: can we just have ->isValid()?
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
