<?php

namespace App\Messenger\Monitor\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
#[AsCommand(
    name: 'messenger:monitor:workers',
    description: 'Display messenger worker status',
)]
final class WorkersCommand extends MonitorCommand
{
    use Refreshable;

    private static function title(): string
    {
        return 'Monitor Messenger Workers';
    }

    private function render(SymfonyStyle $io, InputInterface $input): int
    {
        $this->renderWorkerStatus($io);

        return $this->workerMonitor->count() ? self::SUCCESS : self::FAILURE;
    }
}
