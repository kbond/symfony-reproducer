<?php

namespace App;

use App\Assert\AssertionFailed;
use App\Assert\Expectation\PrimaryExpectation;
use App\Assert\Handler;
use App\Assert\Handler\DefaultHandler;
use App\Assert\Handler\PHPUnitHandler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Assert
{
    private static Handler $handler;

    private function __construct()
    {
    }

    /**
     * Execute an assertion.
     *
     * @param callable $assertion Considered a "pass" if invoked successfully
     *                            Considered a "fail" if {@see AssertionFailed} is thrown
     */
    public static function run(callable $assertion): void
    {
        try {
            $assertion();

            self::handler()->onSuccess();
        } catch (AssertionFailed $e) {
            self::handler()->onFailure($e);
        }
    }

    /**
     * Trigger a generic assertion "pass".
     */
    public static function pass(): void
    {
        self::handler()->onSuccess();
    }

    /**
     * Trigger a generic assertion failure.
     *
     * @param array<string,mixed> $context
     *
     * @return never
     *
     * @throws \Throwable
     */
    public static function fail(string $message, array $context = []): void
    {
        self::run($e = new AssertionFailed($message, $context));

        throw $e;
    }

    /**
     * Execute a callback and return the result. If an exception is thrown,
     * trigger a "fail", if not, trigger a "pass".
     *
     * @template T
     *
     * @param callable():T        $callback Considered a "pass" if invoked successfully
     *                                      Considered a "fail" if an exception is thrown
     * @param string|null         $message  If not passed, use thrown exception message.
     *                                      Available context: {exception}, {message}
     * @param array<string,mixed> $context
     *
     * @return T The return value of executing $callback
     *
     * @throws \Throwable The exception thrown by the callback
     */
    public static function try(callable $callback, ?string $message = null, array $context = []): mixed
    {
        try {
            $ret = $callback();
        } catch (\Throwable $e) {
            self::run(new AssertionFailed(
                $message ?? $e->getMessage(),
                array_merge(['exception' => $e, 'message' => $e->getMessage()], $context),
                $e,
            ));

            throw $e;
        }

        self::pass();

        return $ret;
    }

    public static function expect(mixed $value): PrimaryExpectation
    {
        return new PrimaryExpectation($value);
    }

    /**
     * Force a specific handler or use a custom one.
     */
    public static function useHandler(Handler $handler): void
    {
        self::$handler = $handler;
    }

    private static function handler(): Handler
    {
        if (isset(self::$handler)) {
            return self::$handler;
        }

        if (PHPUnitHandler::isSupported()) {
            return self::$handler = new PHPUnitHandler();
        }

        return self::$handler = new DefaultHandler();
    }
}
