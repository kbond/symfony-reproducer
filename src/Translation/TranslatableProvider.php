<?php

namespace App\Translation;

use Zenstruck\Collection;

/**
 * Implement on your translatable object repository to
 * customize the objects that are warmed/exported.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface TranslatableProvider
{
    public function translatableObjects(): Collection;
}
