<?php

namespace App\Remote;

use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\ServiceCollectionInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ButtonRemote
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

    /**
     * @return iterable<string, ButtonInterface>
     */
    public function buttons(): iterable
    {
        return $this->buttons;
    }
}
