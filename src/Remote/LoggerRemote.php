<?php

namespace App\Remote;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsDecorator(ButtonRemote::class)]
final class LoggerRemote implements RemoteInterface
{
    public function __construct(
        private RemoteInterface $inner,
        private LoggerInterface $logger,
    ) {
    }

    public function press(string $id): void
    {
        $this->logger->info('Pressing button "{id}"', ['id' => $id]);

        $this->inner->press($id);

        $this->logger->info('Pressed button "{id}"', ['id' => $id]);
    }

    public function buttons(): iterable
    {
        return $this->inner->buttons();
    }
}
