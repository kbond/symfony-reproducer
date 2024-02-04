<?php

namespace App\Assert\Expectation;

use App\Assert\Assertion\Contains;
use App\Assert\Assertion\HasCount;
use App\Assert\Assertion\IsEmpty;
use App\Assert\Assertion\IsTrue;
use App\Assert\Assertion\Throws;
use App\Assert\AssertionFailed;
use App\Assert\Expectation;
use App\Assert\Expectation\Types\SizeExpectations;

use function App\Assert\fail;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @extends Expectation<mixed>
 *
 * @phpstan-import-type Context from AssertionFailed
 */
final class PrimaryExpectation extends Expectation
{
    use SizeExpectations;

    public function count(): CountExpectation
    {
        if ($this->isNegated()) {
            throw new \LogicException('Cannot count a negated expectation.');
        }

        if (!$this->what instanceof \Countable && !is_iterable($this->what)) {
            fail('Expected {value} to be countable.', ['value' => $this->what]);
        }

        return new CountExpectation(is_countable($this->what) ? \count($this->what) : iterator_count($this->what));
    }

    /**
     * @param string  $message Available context: {expected}, {actual}
     * @param Context $context
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
     * @param string  $message Available context: {value}
     * @param Context $context
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
     * @param string  $message Available context: {value}
     * @param Context $context
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
     * @param string  $message Available context: {value}
     * @param Context $context
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
     * @param string  $message Available context: {value}
     * @param Context $context
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
     * @param string  $message Available context: {value}
     * @param Context $context
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
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeEmpty(string $message = 'Expected {value} to <NOT>be empty.', array $context = []): self
    {
        return $this->run(new IsEmpty($this->what, $message, $context));
    }

    /**
     * @param string  $message Available context: {needle}, {haystack}
     * @param Context $context
     */
    public function toContain(mixed $needle, string $message = 'Expected {haystack} to <NOT>contain {needle}.', array $context = []): self
    {
        return $this->run(
            new Contains($needle, $this->what, message: $message, context: $context)
        );
    }

    /**
     * @param string  $message Available context: {value}, {count}
     * @param Context $context
     */
    public function toHaveCount(int $count, string $message = 'Expected {value} to <NOT>have a count of {count}.', array $context = []): self
    {
        return $this->run(new HasCount($this->what, $count, $message, $context));
    }

    /**
     * @param class-string $class
     * @param string       $message Available context: {value}, {class}
     * @param Context      $context $context
     */
    public function toBeAnInstanceOf(string $class, string $message = 'Expected {value} to <NOT>be an instance of {class}.', array $context = []): self
    {
        return $this->run(new IsTrue(
            $this->what instanceof $class,
            $message,
            array_merge($context, ['value' => $this->what, 'class' => $class])
        ));
    }

    /**
     * Executes the expectation value as a callable and asserts the $expectedException is thrown. When
     * $expectedException is a callable, it is executed with the caught exception enabling additional
     * assertions within. Optionally pass $expectedMessage to assert the caught exception contains
     * this value.
     *
     * @param class-string<\Throwable>|callable(\Throwable):void $expectedException string: class name of the expected exception
     *                                                                              callable: uses the first argument's type-hint
     *                                                                              to determine the expected exception class. When
     *                                                                              exception is caught, callable is invoked with
     *                                                                              the caught exception
     * @param string|null                                        $expectedMessage   Assert the caught exception message "contains"
     *                                                                              this string
     */
    public function toThrow(string|callable $expectedException, ?string $expectedMessage = null): self
    {
        if (!\is_callable($this->what)) {
            fail('Expected {value} to be callable.', ['value' => $this->what]);
        }

        $this->run(new Throws($this->what, $expectedException, $expectedMessage));

        return $this;
    }

    public function and(mixed $what = '__SAME_VALUE__'): static
    {
        if ('__SAME_VALUE__' === $what) {
            return parent::and();
        }

        return new self($what);
    }
}
