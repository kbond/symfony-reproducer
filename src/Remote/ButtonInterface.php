<?php

namespace App\Remote;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[Autoconfigure(
    tags: [self::class],
)]
interface ButtonInterface
{
    public function press(): void;
}
