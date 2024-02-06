<?php

namespace App\Assert;

use App\Assert;
use App\Assert\Assertion\Conditional;
use App\Assert\Assertion\IsTrue;
use App\Assert\Assertion\Not;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @template T
 *
 * @phpstan-import-type Context from AssertionFailed
 */
abstract class Expectation
{
    use Dumpable;

    private bool $negate = false;

    /**
     * @param T $value
     */
    final public function __construct(public readonly mixed $value)
    {
    }

    /**
     * @param string  $message Available context: {expected}, {actual}
     * @param Context $context
     */
    final public function toBe(mixed $actual, string $message = 'Expected {expected} to <NOT>be the same as {actual}.', array $context = []): static
    {
        return $this->ensureTrue(
            $actual === $this->value,
            $message,
            array_merge($context, ['expected' => $this->value, 'actual' => $actual])
        );
    }

    final public function not(): static
    {
        $this->negate = true;

        return $this;
    }

    public function and(): static
    {
        return $this->reset();
    }

    final public function andNot(): static
    {
        return $this->and()->not();
    }

    /**
     * @param Context $context
     */
    final protected function ensureTrue(bool $condition, string $message, array $context = []): static
    {
        return $this->run(new IsTrue($condition, $message, $context));
    }

    final protected function run(callable|Conditional $assertion): static
    {
        if ($this->negate && $assertion instanceof Conditional) {
            $assertion = new Not($assertion);
        }

        if ($this->negate && !$assertion instanceof Conditional) {
            throw new \LogicException(sprintf('Cannot negate non-conditional assertion "%s".', get_debug_type($assertion)));
        }

        Assert::run($assertion);

        return $this->reset();
    }

    /**
     * @template S of self
     *
     * @param S $expectation
     *
     * @return S
     */
    final protected function transform(self $expectation): self
    {
        if ($this->negate) {
            throw new \LogicException(sprintf('Cannot created sub-expectation (%s) when negated.', $expectation::class));
        }

        return $expectation;
    }

    protected function dumpValue(): mixed
    {
        return $this->value;
    }

    private function reset(): static
    {
        $this->negate = false;

        return $this;
    }
}
