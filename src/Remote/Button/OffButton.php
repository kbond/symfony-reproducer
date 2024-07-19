<?php

namespace App\Remote\Button;

use App\Remote\ButtonInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsTaggedItem('off')]
final class OffButton implements ButtonInterface
{
    public function press(): void
    {
        dump('off logic');
    }
}
