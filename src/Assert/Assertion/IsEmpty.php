<?php

namespace App\Assert\Assertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class IsEmpty extends Conditional
{
    public function __construct(
        private mixed $value,
        string $message = 'Expected {value} to <NOT>be empty.',
        array $context = [],
    ) {
        parent::__construct($message, array_merge($context, ['value' => $this->value]));
    }

    protected function evalulate(): bool
    {
        if (\is_countable($this->value)) {
            return 0 === \count($this->value);
        }

        if (\is_iterable($this->value)) {
            return 0 === \iterator_count($this->value);
        }

        return empty($this->value);
    }
}
