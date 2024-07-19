<?php

namespace App\Remote\Button;

use App\Remote\ButtonInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class OffButton implements ButtonInterface
{
    public function press(): void
    {
        dump('off logic');
    }
}
