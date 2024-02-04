<?php

namespace App\Assert\Assertion;

use App\Assert;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class HasCount extends Conditional
{
    public function __construct(
        private mixed $value,
        private int $count,
        string $message = 'Expected {value} to <NOT>have a count of {count}.',
        array $context = [],
    ) {
        parent::__construct($message, array_merge($context, ['value' => $this->value, 'count' => $this->count]));
    }

    protected function evalulate(): bool
    {
        if (is_countable($this->value)) {
            return $this->count === \count($this->value);
        }

        if (is_iterable($this->value)) {
            return $this->count === iterator_count($this->value);
        }

        if (\is_string($this->value)) {
            return $this->count === \strlen($this->value);
        }

        Assert::fail('{value} is not countable.', ['value' => $this->value]);
    }
}
