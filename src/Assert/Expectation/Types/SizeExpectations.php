<?php

namespace App\Assert\Expectation\Types;

use App\Assert\AssertionFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 *
 * @phpstan-import-type Context from AssertionFailed
 */
trait SizeExpectations
{
    /**
     * @param string  $message Available context: {expected}, {actual}
     * @param Context $context
     */
    public function toBeGreaterThan(mixed $actual, string $message = 'Expected {expected} to <NOT>be greater than {actual}.', array $context = []): self
    {
        return $this->ensureTrue(
            $this->what > $actual,
            $message,
            array_merge($context, ['expected' => $this->what, 'actual' => $actual])
        );
    }

    /**
     * @param string  $message Available context: {expected}, {actual}
     * @param Context $context
     */
    public function toBeLessThan(mixed $actual, string $message = 'Expected {expected} to <NOT>be less than {actual}.', array $context = []): self
    {
        return $this->ensureTrue(
            $this->what < $actual,
            $message,
            array_merge($context, ['expected' => $this->what, 'actual' => $actual])
        );
    }

    /**
     * @param string  $message Available context: {expected}, {actual}
     * @param Context $context
     */
    public function toBeGreaterThanOrEqualTo(mixed $actual, string $message = 'Expected {expected} to <NOT>be greater than or equal to {actual}.', array $context = []): self
    {
        return $this->ensureTrue(
            $this->what >= $actual,
            $message,
            array_merge($context, ['expected' => $this->what, 'actual' => $actual])
        );
    }

    /**
     * @param string  $message Available context: {expected}, {actual}
     * @param Context $context
     */
    public function toBeLessThanOrEqualTo(mixed $actual, string $message = 'Expected {expected} to <NOT>be less than or equal to {actual}.', array $context = []): self
    {
        return $this->ensureTrue(
            $this->what <= $actual,
            $message,
            array_merge($context, ['expected' => $this->what, 'actual' => $actual])
        );
    }
}
