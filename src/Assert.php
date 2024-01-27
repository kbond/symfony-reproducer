<?php

namespace App;

use App\Assert\AssertionFailed;
use App\Assert\Handler;

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

    public static function true(bool $condition, string $message, array $context = []): void
    {
        $condition ? self::pass() : self::fail($message, $context);
    }

    public static function false(bool $condition, string $message, array $context = [])
    {
        self::true(!$condition, $message, $context);
    }

    /**
     * Trigger a generic assertion failure.
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
     * Trigger a generic assertion "pass".
     */
    public static function pass(): void
    {
        self::handler()->onSuccess();
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
    public static function try(callable $callback, ?string $message = null, array $context = [])
    {
        try {
            $ret = $callback();
        } catch (\Throwable $e) {
            self::run(new AssertionFailed(
                $message ?? $e->getMessage(),
                \array_merge(['exception' => $e, 'message' => $e->getMessage()], $context),
                $e,
            ));

            throw $e;
        }

        self::pass();

        return $ret;
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

        if (Handler\PHPUnitHandler::isSupported()) {
            return self::$handler = new Handler\PHPUnitHandler();
        }

        return self::$handler = new Handler\DefaultHandler();
    }
}
