<?php

namespace App\Remote\Button;

use App\Remote\ButtonInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class VolumeUpButton implements ButtonInterface
{
    public function press(): void
    {
        dump('volume-up logic');
    }
}
