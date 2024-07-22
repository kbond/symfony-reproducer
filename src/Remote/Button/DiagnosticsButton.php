<?php

namespace App\Remote\Button;

use App\Remote\ButtonInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsTaggedItem('diagnostics', priority: -10)]
#[When('prod')]
final class DiagnosticsButton implements ButtonInterface
{
    public function press(): void
    {
        dump('diagnostics logic');
    }
}
