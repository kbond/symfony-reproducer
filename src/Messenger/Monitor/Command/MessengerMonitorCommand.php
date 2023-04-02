<?php

namespace App\Messenger\Monitor\Command;

use App\Messenger\Monitor\Statistics;
use App\Messenger\Monitor\Storage\FilterBuilder;
use App\Messenger\Monitor\Worker\Monitor;
use App\Messenger\Monitor\Worker\Status;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'messenger:monitor',
    description: 'Monitor your messenger workers and transports'
)]
class MessengerMonitorCommand extends Command
{
    public function __construct(private Monitor $monitor, private Statistics $statistics)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('transport', 't', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Transport names to view (all by default)', [])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $transports = $input->getOption('transport');
        $io = new SymfonyStyle($input, $output);
        $io->title('Messenger Monitor');

        if (!$input->isInteractive() || !$output instanceof ConsoleOutputInterface) {
            $this->renderWorkerStatus($io);
            $io->newLine();
            $this->renderStatistics($io, $transports);

            return Command::SUCCESS;
        }

        $io = new SymfonyStyle($input, $section = $output->section());

        while (true) {
            $this->renderWorkerStatus($io);
            $io->writeln('');
            $this->renderStatistics($io, $transports);
            $io->writeln('<comment>! [NOTE] Press CTRL+C to quit</comment>');

            \sleep(1);
            $section->clear();
        }
    }

    private function renderStatistics(SymfonyStyle $io, array $transports): void
    {
        if (!$transports) {
            $this->renderStatisticsTable($io);
            $io->writeln('');

            return;
        }

        foreach ($transports as $transport) {
            $this->renderStatisticsTable($io, $transport);
            $io->writeln('');
        }
    }

    private function renderStatisticsTable(SymfonyStyle $io, ?string $transport = null): void
    {
        $filter = FilterBuilder::lastDay();
        $title = 'Statistics';

        if ($transport) {
            $filter = $filter->on($transport);
            $title .= \sprintf(' (%s)', $transport);
        }

        $snapshot = $this->statistics->snapshot($filter);
        $failRate = \round($snapshot->failRate() * 100);
        $table = $io->createTable()
            ->setHorizontal()
            ->setHeaderTitle($title)
            ->setHeaders([
                'Period',
                'Messages Processed',
                'Fail Rate',
                'Avg. Wait Time',
                'Avg. Handling Time',
                'Handled Per Minute',
                'Handled Per Hour',
                'Handled Per Day',
            ])
            ->addRow([
                'Last Day',
                $snapshot->totalCount(),
                match (true) {
                     $failRate < 5 => \sprintf('<info>%s%%</info>', $failRate),
                     $failRate < 10 => \sprintf('<comment>%s%%</comment>', $failRate),
                     default => \sprintf('<error>%s%%</error>', $failRate),
                },
                Helper::formatTime($snapshot->averageWaitTime()),
                Helper::formatTime($snapshot->averageHandlingTime()),
                \round($snapshot->handledPerMinute(), 2),
                \round($snapshot->handledPerHour(), 2),
                \round($snapshot->handledPerDay(), 2),
            ])
        ;

        $table->render();
    }

    private function renderWorkerStatus(SymfonyStyle $io): void
    {
        $table = $io->createTable()
            ->setHeaderTitle('Messenger Workers')
            ->setHeaders(['PID', 'Status', 'Transports', 'Queues'])
        ;

        if (!$workers = $this->monitor->all()) {
            $table->addRow([new TableCell('<error>[!] No workers running.</error>', [
                'colspan' => 4,
                'style' => new TableCellStyle(['align' => 'center']),
            ])]);
            $table->render();

            return;
        }

        $table->addRows(\array_map(
            static fn (Status $status, int $pid) => [
                $pid,
                \sprintf('<%s>%s</>', $status->isProcessing() ? 'comment' : 'info', $status->status()),
                implode(', ', $status->transports()),
                implode(', ', $status->queues()) ?: 'n/a',
            ],
            $workers,
            \array_keys($workers)
        ));

        $table->render();
    }
}
