<?php

namespace App\Remote;

use App\Remote\Button\OffButton;
use App\Remote\Button\OnButton;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use App\Remote\Button\VolumeUpButton;
use App\Remote\Button\VolumeDownButton;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ButtonRemote
{
    public function __construct(
        #[AutowireLocator([
            'on' => OnButton::class,
            'off' => OffButton::class,
            'volume-up' => VolumeUpButton::class,
            'volume-down' => VolumeDownButton::class,
        ])]
        private ContainerInterface $buttons,
    ) {
    }

    public function press(string $id): void
    {
        $this->buttons->get($id)->press();
    }
}
