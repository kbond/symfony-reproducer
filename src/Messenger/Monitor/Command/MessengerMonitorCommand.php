<?php

namespace App\Messenger\Monitor\Command;

use App\Messenger\Monitor\Statistics;
use App\Messenger\Monitor\Storage\Specification;
use App\Messenger\Monitor\WorkerMonitor;
use App\Messenger\Monitor\Worker\WorkerStatus;
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
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'messenger:monitor',
    description: 'Monitor your messenger workers and transports'
)]
class MessengerMonitorCommand extends Command
{
    public function __construct(
        private WorkerMonitor $monitor,
        private Statistics $statistics,

        /**
         * @var string[]
         */
        #[Autowire(param: 'zenstruck_messenger_monitor.transport_names')]
        private array $transportNames,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'The start date of the statistics', Specification::ONE_DAY_AGO, Specification::DATE_PRESETS)
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'The end date of the statistics')
            ->addOption('message-type', null, InputOption::VALUE_REQUIRED, 'Filter by message type')
            ->addOption('transport', null, InputOption::VALUE_REQUIRED, 'Filter by transport name', null, $this->transportNames)
            ->addOption('tag', null, InputOption::VALUE_REQUIRED, 'Filter by a tag')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $specification = Specification::fromArray([
            'from' => $from = $input->getOption('from'),
            'to' => $input->getOption('to'),
            'message_type' => $input->getOption('message-type'),
            'transport' => $input->getOption('transport'),
            'tag' => $input->getOption('tag'),
        ]);

        $io->title('Messenger Monitor');

        if (!$input->isInteractive() || !$output instanceof ConsoleOutputInterface) {
            $this->renderWorkerStatus($io);
            $io->newLine();
            $this->renderStatistics($io, $from, $specification);

            return Command::SUCCESS;
        }

        $io = new SymfonyStyle($input, $section = $output->section());

        while (true) {
            $this->renderWorkerStatus($io);
            $io->writeln('');
            $this->renderStatistics($io, $from, $specification);
            $io->writeln('<comment>! [NOTE] Press CTRL+C to quit</comment>');

            \sleep(1);
            $section->clear();
        }
    }

    private function renderStatistics(SymfonyStyle $io, string $fromInput, Specification $specification): void
    {
        $snapshot = $this->statistics->snapshot($specification);
        $failRate = \round($snapshot->failRate() * 100);
        $toTimestamp = $specification->toArray()['to'];
        $period = match(true) {
            !$toTimestamp && $fromInput === Specification::ONE_HOUR_AGO => 'Last Hour',
            !$toTimestamp && $fromInput === Specification::ONE_DAY_AGO => 'Last 24 Hours',
            !$toTimestamp && $fromInput === Specification::ONE_WEEK_AGO => 'Last 7 Days',
            !$toTimestamp && $fromInput === Specification::ONE_MONTH_AGO => 'Last 30 Days',
            !$toTimestamp => \sprintf('From %s to now', $specification->toArray()['from']->format('Y-m-d H:i:s')),
            default => \sprintf('From %s to %s', $specification->toArray()['from']->format('Y-m-d H:i:s'), $toTimestamp->format('Y-m-d H:i:s')),
        };
        $waitTime = $snapshot->averageWaitTime();
        $handlingTime = $snapshot->averageHandlingTime();
        $table = $io->createTable()
            ->setHorizontal()
            ->setHeaderTitle('Statistics')
            ->setHeaders([
                'Period',
                'Transport(s)',
                'Messages Processed',
                'Fail Rate',
                'Avg. Wait Time',
                'Avg. Handling Time',
                'Handled Per Minute',
                'Handled Per Hour',
                'Handled Per Day',
            ])
            ->addRow([
                $period,
                $specification->toArray()['transport'] ?? \implode(', ', $this->transportNames),
                $snapshot->totalCount(),
                match (true) {
                     $failRate < 5 => \sprintf('<info>%s%%</info>', $failRate),
                     $failRate < 10 => \sprintf('<comment>%s%%</comment>', $failRate),
                     default => \sprintf('<error>%s%%</error>', $failRate),
                },
                $waitTime ? Helper::formatTime($snapshot->averageWaitTime()) : 'n/a',
                $handlingTime ? Helper::formatTime($snapshot->averageHandlingTime()) : 'n/a',
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
            static fn (WorkerStatus $status, int $pid) => [
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
