<?php

namespace App\Remote;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ButtonRemote
{
    public function __construct(
        #[AutowireIterator(ButtonInterface::class, indexAttribute: 'key')]
        private iterable $buttons,
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
