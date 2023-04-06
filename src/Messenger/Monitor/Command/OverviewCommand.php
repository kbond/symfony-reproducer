<?php

namespace App\Messenger\Monitor\Command;

use App\Messenger\Monitor\Storage\Specification;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
#[AsCommand(
    name: 'messenger:monitor:overview',
    description: 'Display an overview of your workers, transports and processed messages',
    aliases: ['messenger:monitor']
)]
class OverviewCommand extends ProcessedFilterCommand
{
    use Refreshable;

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'The start date of the statistics', Specification::ONE_DAY_AGO, Specification::DATE_PRESETS)
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'The end date of the statistics')
        ;

        parent::configure();
    }

    private static function title(): string
    {
        return 'Messenger Monitor Overview';
    }

    private function render(SymfonyStyle $io, InputInterface $input): int
    {
        $specification = self::createSpecification(
            $input,
            from: $input->getOption('from'),
            to: $input->getOption('to'),
        );

        $this->renderWorkerStatus($io);
        $io->writeln('');
        $this->renderTransportStatus($io, $this->transportMonitor);
        $io->writeln('');
        $this->renderStatistics($io, $input, $specification);

        return self::SUCCESS;
    }
}
