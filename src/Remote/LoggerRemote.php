<?php

namespace App\Remote;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\Target;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsDecorator(ButtonRemote::class)]
final class LoggerRemote implements RemoteInterface
{
    public function __construct(
        private RemoteInterface $inner,
        #[Target('buttonsLogger')]
        private LoggerInterface $buttonLogger,
    ) {
    }

    public function press(string $id): void
    {
        $this->buttonLogger->info('Pressing button "{id}"', ['id' => $id]);

        $this->inner->press($id);

        $this->buttonLogger->info('Pressed button "{id}"', ['id' => $id]);
    }

    public function buttons(): iterable
    {
        return $this->inner->buttons();
    }
}
