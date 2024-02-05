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
    private bool $negate = false;

    /**
     * @param T $what
     */
    final public function __construct(protected readonly mixed $what)
    {
    }

    /**
     * @param string  $message Available context: {expected}, {actual}
     * @param Context $context
     */
    final public function toBe(mixed $actual, string $message = 'Expected {expected} to <NOT>be the same as {actual}.', array $context = []): static
    {
        return $this->run(new IsTrue(
            $actual === $this->what,
            $message,
            array_merge($context, ['expected' => $this->what, 'actual' => $actual])
        ));
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

    final protected function reset(): static
    {
        $this->negate = false;

        return $this;
    }

    final protected function ensureNotNegated(string $method): static
    {
        if ($this->negate) {
            throw new \LogicException(sprintf('Cannot call "%s::%s()" on a negated expectation.', static::class, $method));
        }

        return $this;
    }
}
