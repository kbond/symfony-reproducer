<?php

namespace App\Assert\Expectation;

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
        return $this
            ->toBeArray()
            ->transform(new ArrayExpectation($this->value)) // @phpstan-ignore-line
        ;
    }

    public function jsonDecode(): self
    {
        return $this
            ->ensureTrue(\is_string($this->value) && json_validate($this->value), 'Expected {value} to be a JSON string.', ['value' => $this->value])
            ->transform(new self(json_decode($this->value, associative: true, flags: \JSON_THROW_ON_ERROR))) // @phpstan-ignore-line
        ;
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
        return $this->ensureTrue(
            $actual == $this->value,
            $message,
            array_merge($context, ['expected' => $this->value, 'actual' => $actual])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeTrue(string $message = 'Expected {value} to <NOT>be true.', array $context = []): self
    {
        return $this->ensureTrue(
            true === $this->value,
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeFalse(string $message = 'Expected {value} to <NOT>be false.', array $context = []): self
    {
        return $this->ensureTrue(
            false === $this->value,
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeTruthy(string $message = 'Expected {value} to <NOT>be true.', array $context = []): self
    {
        return $this->ensureTrue(
            true == $this->value,
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeFalsy(string $message = 'Expected {value} to <NOT>be falsy.', array $context = []): self
    {
        return $this->ensureTrue(
            false == $this->value,
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeNull(string $message = 'Expected {value} to <NOT>be null.', array $context = []): self
    {
        return $this->ensureTrue(
            null === $this->value,
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeString(string $message = 'Expected {value} to <NOT>be a string.', array $context = []): self
    {
        return $this->ensureTrue(
            \is_string($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeInt(string $message = 'Expected {value} to <NOT>be an integer.', array $context = []): self
    {
        return $this->ensureTrue(
            \is_int($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeFloat(string $message = 'Expected {value} to <NOT>be a float.', array $context = []): self
    {
        return $this->ensureTrue(
            \is_float($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeBool(string $message = 'Expected {value} to <NOT>be a boolean.', array $context = []): self
    {
        return $this->ensureTrue(
            \is_bool($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeObject(string $message = 'Expected {value} to <NOT>be an object.', array $context = []): self
    {
        return $this->ensureTrue(
            \is_object($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeResource(string $message = 'Expected {value} to <NOT>be a resource.', array $context = []): self
    {
        return $this->ensureTrue(
            \is_resource($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeArray(string $message = 'Expected {value} to <NOT>be an array.', array $context = []): self
    {
        return $this->ensureTrue(
            \is_array($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeCallable(string $message = 'Expected {value} to <NOT>be callable.', array $context = []): self
    {
        return $this->ensureTrue(
            \is_callable($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeIterable(string $message = 'Expected {value} to <NOT>be iterable.', array $context = []): self
    {
        return $this->ensureTrue(
            is_iterable($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeCountable(string $message = 'Expected {value} to <NOT>be countable.', array $context = []): self
    {
        return $this->ensureTrue(
            is_countable($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeNumeric(string $message = 'Expected {value} to <NOT>be numeric.', array $context = []): self
    {
        return $this->ensureTrue(
            is_numeric($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeScalar(string $message = 'Expected {value} to <NOT>be scalar.', array $context = []): self
    {
        return $this->ensureTrue(
            \is_scalar($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeStringable(string $message = 'Expected {value} to <NOT>be "stringable" (scalar|Stringable|null).', array $context = []): self
    {
        return $this->ensureTrue(
            \is_string($this->value) || $this->value instanceof \Stringable,
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param class-string $class
     * @param string       $message Available context: {value}, {class}
     * @param Context      $context $context
     */
    public function toBeInstanceOf(string $class, string $message = 'Expected {value} to <NOT>be an instance of {class}.', array $context = []): self
    {
        return $this->ensureTrue(
            $this->value instanceof $class,
            $message,
            array_merge($context, ['value' => $this->value, 'class' => $class])
        );
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
        return $this
            ->ensureTrue(\is_callable($this->value), 'Expected {value} to be callable.', ['value' => $this->value])
            ->run(new Throws($this->value, $expectedException, $expectedMessage)) // @phpstan-ignore-line
        ;
    }

    public function and(mixed $value = '__SAME_VALUE__'): static
    {
        if ('__SAME_VALUE__' === $value) {
            return parent::and();
        }

        return new self($value);
    }
}
