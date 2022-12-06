<?php

namespace App;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AutoconfigureTag('some_tag')]
interface SomeInterface
{
}
