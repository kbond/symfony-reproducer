<?php

namespace App\Assert;

use App\Assert\Handler\HandlerManager;

function expect(mixed $what): Expectation
{
    return new Expectation($what);
}

/**
 * Execute a callback and return the result. If an exception is thrown,
 * trigger a "fail", if not, trigger a "pass".
 *
 * @template T
 *
 * @param callable():T $callback Considered a "pass" if invoked successfully
 *                               Considered a "fail" if an exception is thrown
 * @param string|null  $message  If not passed, use thrown exception message.
 *                               Available context: {exception}, {message}
 *
 * @return T The return value of executing $callback
 *
 * @throws \Throwable The exception thrown by the callback
 */
function attempt(callable $callback, ?string $message = null, array $context = []): mixed
{
    try {
        $ret = $callback();
    } catch (\Throwable $e) {
        run(new AssertionFailed(
            $message ?? $e->getMessage(),
            \array_merge(['exception' => $e, 'message' => $e->getMessage()], $context),
            $e,
        ));

        throw $e;
    }

    pass();

    return $ret;
}

/**
 * Execute an assertion.
 *
 * @param callable $assertion Considered a "pass" if invoked successfully
 *                            Considered a "fail" if {@see AssertionFailed} is thrown
 */
function run(callable $assertion): void
{
    try {
        $assertion();

        HandlerManager::get()->onSuccess();
    } catch (AssertionFailed $e) {
        HandlerManager::get()->onFailure($e);
    }
}

/**
 * Trigger a generic assertion "pass".
 */
function pass(): void
{
    HandlerManager::get()->onSuccess();
}

/**
 * Trigger a generic assertion failure.
 *
 * @return never
 *
 * @throws \Throwable
 */
function fail(string $message, array $context = []): void
{
    run($e = new AssertionFailed($message, $context));

    throw $e;
}
