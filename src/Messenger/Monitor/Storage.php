<?php

namespace App\Messenger\Monitor;

use Symfony\Component\Messenger\Envelope;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Storage
{
    public function save(Envelope $envelope, ?\Throwable $exception = null): void;
}
