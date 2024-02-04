<?php

namespace App\Assert\Handler;

use App\Assert\Handler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class HandlerManager
{
    private static Handler $handler;

    public static function get(): Handler
    {
        if (isset(self::$handler)) {
            return self::$handler;
        }

        if (PHPUnitHandler::isSupported()) {
            return self::$handler = new PHPUnitHandler();
        }

        return self::$handler = new DefaultHandler();
    }

    /**
     * Force a specific handler or use a custom one.
     */
    public static function useHandler(Handler $handler): void
    {
        self::$handler = $handler;
    }
}
