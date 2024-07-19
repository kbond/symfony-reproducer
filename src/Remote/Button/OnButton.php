<?php

namespace App\Remote\Button;

use App\Remote\ButtonInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class OnButton implements ButtonInterface
{
    public function press(): void
    {
        dump('on logic');
    }
}
