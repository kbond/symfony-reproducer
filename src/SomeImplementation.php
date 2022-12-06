<?php

namespace App;

use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsTaggedItem(priority: 10)]
final class SomeImplementation implements SomeInterface
{
}
