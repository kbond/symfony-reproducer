<?php

namespace App\Assert\Handler;

use App\Assert\AssertionFailed;
use App\Assert\Handler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class DefaultHandler implements Handler
{
    public function onSuccess(): void
    {
        // noop
    }

    public function onFailure(AssertionFailed $exception): void
    {
        throw $exception;
    }
}
