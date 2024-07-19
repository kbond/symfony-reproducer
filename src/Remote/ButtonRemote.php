<?php

namespace App\Remote;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ButtonRemote
{
    public function __construct(
        #[AutowireLocator(ButtonInterface::class)]
        private ContainerInterface $buttons,
    ) {
    }

    public function press(string $id): void
    {
        $this->buttons->get($id)->press();
    }
}
