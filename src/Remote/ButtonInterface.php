<?php

namespace App\Remote;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AutoconfigureTag]
interface ButtonInterface
{
    public function press(): void;
}
