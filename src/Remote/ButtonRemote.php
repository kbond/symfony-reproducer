<?php

namespace App\Remote;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\ServiceCollectionInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsAlias]
final class ButtonRemote implements RemoteInterface
{
    public function __construct(
        #[AutowireLocator(ButtonInterface::class)]
        private ServiceCollectionInterface $buttons,
    ) {
    }

    public function press(string $id): void
    {
        $this->buttons->get($id)->press();
    }

    public function buttons(): iterable
    {
        return $this->buttons;
    }
}
