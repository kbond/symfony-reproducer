<?php

namespace App\Assert;

use App\Assert\Assertion\Conditional;
use App\Assert\Assertion\Contains;
use App\Assert\Assertion\IsEmpty;
use App\Assert\Assertion\IsTrue;
use App\Assert\Assertion\Not;
use App\Assert\Assertion\HasCount;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Expectation
{
    private bool $negate = false;

    public function __construct(private mixed $what)
    {
    }

    /**
     * @param string $message Available context: {expected}, {actual}
     */
    public function toEqual(mixed $actual, string $message = 'Expected {expected} to <NOT>equal {actual}.', array $context = []): self
    {
        return $this->run(new IsTrue(
            $actual == $this->what,
            $message,
            array_merge($context, ['expected' => $this->what, 'actual' => $actual])
        ));
    }

    /**
     * @param string $message Available context: {expected}, {actual}
     */
    public function toBe(mixed $actual, string $message = 'Expected {expected} to <NOT>be the same as {actual}.', array $context = []): self
    {
        return $this->run(new IsTrue(
            $actual === $this->what,
            $message,
            array_merge($context, ['expected' => $this->what, 'actual' => $actual])
        ));
    }

    /**
     * @param string $message Available context: {expected}, {actual}
     */
    public function toBeGreaterThan(mixed $actual, string $message = 'Expected {expected} to <NOT>be greater than {actual}.', array $context = []): self
    {
        return $this->run(new IsTrue(
            $this->what > $actual,
            $message,
            array_merge($context, ['expected' => $this->what, 'actual' => $actual])
        ));
    }

    /**
     * @param string $message Available context: {expected}, {actual}
     */
    public function toBeLessThan(mixed $actual, string $message = 'Expected {expected} to <NOT>be less than {actual}.', array $context = []): self
    {
        return $this->run(new IsTrue(
            $this->what < $actual,
            $message,
            array_merge($context, ['expected' => $this->what, 'actual' => $actual])
        ));
    }

    /**
     * @param string $message Available context: {expected}, {actual}
     */
    public function toBeGreaterThanOrEqualTo(mixed $actual, string $message = 'Expected {expected} to <NOT>be greater than or equal to {actual}.', array $context = []): self
    {
        return $this->run(new IsTrue(
            $this->what >= $actual,
            $message,
            array_merge($context, ['expected' => $this->what, 'actual' => $actual])
        ));
    }

    /**
     * @param string $message Available context: {expected}, {actual}
     */
    public function toBeLessThanOrEqualTo(mixed $actual, string $message = 'Expected {expected} to <NOT>be less than or equal to {actual}.', array $context = []): self
    {
        return $this->run(new IsTrue(
            $this->what <= $actual,
            $message,
            array_merge($context, ['expected' => $this->what, 'actual' => $actual])
        ));
    }

    /**
     * @param string $message Available context: {value}
     */
    public function toBeTrue(string $message = 'Expected {value} to <NOT>be true.', array $context = []): self
    {
        return $this->run(new IsTrue(
            true === $this->what,
            $message,
            array_merge($context, ['value' => $this->what])
        ));
    }

    /**
     * @param string $message Available context: {value}
     */
    public function toBeFalse(string $message = 'Expected {value} to <NOT>be false.', array $context = []): self
    {
        return $this->run(new IsTrue(
            false === $this->what,
            $message,
            array_merge($context, ['value' => $this->what])
        ));
    }

    /**
     * @param string $message Available context: {value}
     */
    public function toBeTruthy(string $message = 'Expected {value} to <NOT>be true.', array $context = []): self
    {
        return $this->run(new IsTrue(
            true == $this->what,
            $message,
            array_merge($context, ['value' => $this->what])
        ));
    }

    /**
     * @param string $message Available context: {value}
     */
    public function toBeFalsy(string $message = 'Expected {value} to <NOT>be falsy.', array $context = []): self
    {
        return $this->run(new IsTrue(
            false == $this->what,
            $message,
            array_merge($context, ['value' => $this->what])
        ));
    }

    /**
     * @param string $message Available context: {value}
     */
    public function toBeNull(string $message = 'Expected {value} to <NOT>be null.', array $context = []): self
    {
        return $this->run(new IsTrue(
            null === $this->what,
            $message,
            array_merge($context, ['value' => $this->what])
        ));
    }

    /**
     * @param string $message Available context: {value}
     */
    public function toBeEmpty(string $message = 'Expected {value} to <NOT>be empty.', array $context = []): self
    {
        return $this->run(new IsEmpty($this->what, $message, $context));
    }

    /**
     * @param string $message Available context: {needle}, {haystack}
     */
    public function toContain(mixed $needle, string $message = 'Expected {haystack} to <NOT>contain {needle}.', array $context = []): self
    {
        return $this->run(
            new Contains($needle, $this->what, message: $message, context: $context)
        );
    }

    /**
     * @param string $message Available context: {value}, {count}
     */
    public function toHaveCount(int $count, string $message = 'Expected {value} to <NOT>have a count of {count}.', array $context = []): self
    {
        return $this->run(new HasCount($this->what, $count, $message, $context));
    }

    public function toBeAnInstanceOf(string $class, string $message = 'Expected {value} to <NOT>be an instance of {class}.', array $context = []): self
    {
        return $this->run(new IsTrue(
            $this->what instanceof $class,
            $message,
            array_merge($context, ['value' => $this->what, 'class' => $class])
        ));
    }

    public function not(): self
    {
        $this->negate = true;

        return $this;
    }

    public function and(mixed $what = '__SAME_VALUE__'): self
    {
        if ('__SAME_VALUE__' === $what) {
            return $this->reset();
        }

        return new self($what);
    }

    public function andNot(): self
    {
        return $this->and()->not();
    }

    private function run(Conditional $assertion): self
    {
        if ($this->negate) {
            $assertion = new Not($assertion);
        }

        run($assertion);

        return $this->reset();
    }

    private function reset(): self
    {
        $this->negate = false;

        return $this;
    }
}
