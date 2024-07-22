<?php

namespace App\Remote\Button;

use App\Remote\ButtonInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\Lazy;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsTaggedItem('on', priority: 10)]
#[Lazy(ButtonInterface::class)]
final class OnButton implements ButtonInterface
{
    public function press(): void
    {
        dump('on logic');
    }
}
