<?php

namespace App\Remote\Button;

use App\Remote\ButtonInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @final
 */
#[AsTaggedItem('volume-up')]
class VolumeUpButton implements ButtonInterface
{
    public function press(): void
    {
        dump('volume-up logic');
    }
}
