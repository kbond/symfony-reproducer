<?php

namespace App\Assert\Expectation\Types;

use App\Assert\Assertion\Contains;
use App\Assert\Assertion\HasCount;
use App\Assert\Assertion\IsEmpty;
use App\Assert\AssertionFailed;
use App\Assert\Expectation\CountExpectation;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 *
 * @phpstan-import-type Context from AssertionFailed
 */
trait TraversableExpectations
{
    public function count(): CountExpectation
    {
        return $this
            ->ensureTrue(
                is_countable($this->value) || is_iterable($this->value),
                'Expected {value} to be countable.',
                ['value' => $this->value]
            )
            ->transform(new CountExpectation(is_countable($this->value) ? \count($this->value) : iterator_count($this->value))) // @phpstan-ignore-line
        ;
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeEmpty(string $message = 'Expected {value} to <NOT>be empty.', array $context = []): self
    {
        return $this->run(new IsEmpty($this->value, $message, $context));
    }

    /**
     * @param string  $message Available context: {needle}, {haystack}
     * @param Context $context
     */
    public function toContain(mixed $needle, string $message = 'Expected {haystack} to <NOT>contain {needle}.', array $context = []): self
    {
        return $this->run(
            new Contains($needle, $this->value, message: $message, context: $context)
        );
    }

    /**
     * @param string  $message Available context: {value}, {count}
     * @param Context $context
     */
    public function toHaveCount(int $count, string $message = 'Expected {value} to <NOT>have a count of {count}.', array $context = []): self
    {
        return $this->run(new HasCount($this->value, $count, $message, $context));
    }
}
