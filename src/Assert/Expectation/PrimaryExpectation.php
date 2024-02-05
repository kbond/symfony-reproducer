<?php

namespace App\Assert\Expectation;

use App\Assert;
use App\Assert\Assertion\IsTrue;
use App\Assert\Assertion\Throws;
use App\Assert\AssertionFailed;
use App\Assert\Expectation;
use App\Assert\Expectation\Types\SizeExpectations;
use App\Assert\Expectation\Types\TraversableExpectations;

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
    use TraversableExpectations;

    public function array(): ArrayExpectation
    {
        $this->ensureNotNegated(__FUNCTION__);

        if (!\is_array($this->what)) {
            Assert::fail('Expected {value} to be an array.', ['value' => $this->what]);
        }

        return new ArrayExpectation($this->what);
    }

    public function jsonDecode(): self
    {
        if (!\is_string($this->what) || !json_validate($this->what)) {
            Assert::fail('Expected {value} to be a JSON string.', ['value' => $this->what]);
        }

        return new self(json_decode($this->what, associative: true, flags: \JSON_THROW_ON_ERROR));
    }

    public function jsonDecodeArray(): ArrayExpectation
    {
        return $this->jsonDecode()->array();
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
            Assert::fail('Expected {value} to be callable.', ['value' => $this->what]);
        }

        return $this
            ->ensureNotNegated(__FUNCTION__)
            ->run(new Throws($this->what, $expectedException, $expectedMessage))
        ;
    }

    public function and(mixed $what = '__SAME_VALUE__'): static
    {
        if ('__SAME_VALUE__' === $what) {
            return parent::and();
        }

        return new self($what);
    }
}
