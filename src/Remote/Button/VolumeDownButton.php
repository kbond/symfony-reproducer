<?php

namespace App\Remote\Button;

use App\Remote\ButtonInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @final
 */
#[AsTaggedItem('volume-down')]
class VolumeDownButton implements ButtonInterface
{
    public function press(): void
    {
        dump('volume-down logic');
    }
}
