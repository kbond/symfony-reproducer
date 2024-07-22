<?php

namespace App\Remote;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface RemoteInterface
{
    public function press(string $id): void;

    /**
     * @return iterable<string, ButtonInterface>
     */
    public function buttons(): iterable;
}
