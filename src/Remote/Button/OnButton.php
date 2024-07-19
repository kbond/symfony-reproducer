<?php

namespace App\Remote\Button;

use App\Remote\ButtonInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsTaggedItem('on')]
final class OnButton implements ButtonInterface
{
    public function press(): void
    {
        dump('on logic');
    }
}
