<?php

namespace App\Assert\Expectation;

use App\Assert\AssertionFailed;
use App\Assert\Expectation;
use App\Assert\Expectation\Types\TraversableExpectations;

use function JmesPath\search;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @extends Expectation<array>
 *
 * @phpstan-import-type Context from AssertionFailed
 */
final class ArrayExpectation extends Expectation
{
    use TraversableExpectations;

    /**
     * @param string $selector JMESPath selector
     */
    public function search(string $selector): PrimaryExpectation
    {
        if (!\function_exists('JmesPath\search')) {
            throw new \LogicException('"mtdowling/jmespath.php" is required (composer require --dev mtdowling/jmespath.php).');
        }

        return $this->transform(new PrimaryExpectation(search($selector, $this->value)));
    }

    public function get(int|string $key): PrimaryExpectation
    {
        return $this
            ->toHave($key)
            ->transform(new PrimaryExpectation($this->value[$key]))
        ;
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeList(string $message = 'Expected {value} to <NOT>be a list.', array $context = []): static
    {
        return $this->ensureTrue(
            array_is_list($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string  $message Available context: {value}
     * @param Context $context
     */
    public function toBeAssoc(string $message = 'Expected {value} to <NOT>be an associative array.', array $context = []): static
    {
        return $this->ensureTrue(
            !array_is_list($this->value),
            $message,
            array_merge($context, ['value' => $this->value])
        );
    }

    /**
     * @param string|mixed[] $haystack
     * @param string         $message  Available context: {needle}, {haystack}
     * @param Context        $context
     */
    public function toBeSubsetOf(string|iterable $haystack, string $message = 'Expected {needle} to <NOT>be a subset of {haystack}.', array $context = []): static
    {
        throw new \BadMethodCallException('Not yet implemented');
    }

    /**
     * @param string|mixed[] $needle
     * @param string         $message Available context: {needle}, {haystack}
     * @param Context        $context
     */
    public function toHaveSubset(string|iterable $needle, string $message = 'Expected {needle} to <NOT>be a subset of {haystack}.', array $context = []): static
    {
        throw new \BadMethodCallException('Not yet implemented');
    }

    /**
     * @param string  $message Available context: {haystack}, {key}
     * @param Context $context
     */
    public function toHave(string|int $key, string $message = 'Expected {haystack} to <NOT>have key {key}.', array $context = []): static
    {
        return $this->ensureTrue(
            \array_key_exists($key, $this->value),
            $message,
            array_merge($context, ['haystack' => $this->value, 'key' => $key])
        );
    }
}
