<?php

namespace App\Messenger\Monitor\Command;

use App\Messenger\Monitor\Statistics;
use App\Messenger\Monitor\Storage;
use App\Messenger\Monitor\Storage\Specification;
use App\Messenger\Monitor\Transport\TransportStatus;
use App\Messenger\Monitor\TransportMonitor;
use App\Messenger\Monitor\Worker\WorkerStatus;
use App\Messenger\Monitor\WorkerMonitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
abstract class MonitorCommand extends Command
{
    public function __construct(
        protected readonly WorkerMonitor $workerMonitor,
        protected readonly TransportMonitor $transportMonitor,
        protected readonly ?Statistics $statistics,
        protected readonly ?Storage $storage,
    ) {
        parent::__construct();
    }

    protected function renderWorkerStatus(SymfonyStyle $io): void
    {
        $table = $io->createTable()
            ->setHeaderTitle('Messenger Workers')
            ->setHeaders(['PID', 'Status', 'Transports', 'Queues'])
        ;

        if (!$workers = $this->workerMonitor->all()) {
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

    protected function renderTransportStatus(SymfonyStyle $io, TransportMonitor $transportMonitor): void
    {
        $table = $io->createTable()
            ->setHeaderTitle('Messenger Transports')
            ->setHeaders(['Name', 'Queued Messages'])
        ;

        if (!$transports = $transportMonitor->all()) {
            $table->addRow([new TableCell('<error>[!] No transports configured.</error>', [
                'colspan' => 4,
                'style' => new TableCellStyle(['align' => 'center']),
            ])]);
            $table->render();

            return;
        }

        $table->addRows(\array_map(
            static fn (TransportStatus $status) => [
                $status->name,
                $status->isCountable() ? \count($status) : '<comment>n/a</comment>',
            ],
            $transports
        ));

        $table->render();
    }

    protected function renderStatistics(SymfonyStyle $io, InputInterface $input, Specification $specification): void
    {
        $from = $input->getOption('from');
        $snapshot = $this->statistics->snapshot($specification);
        $failRate = \round($snapshot->failRate() * 100);
        $toTimestamp = $specification->toArray()['to'];
        $period = match(true) {
            !$toTimestamp && $from === Specification::ONE_HOUR_AGO => 'Last Hour',
            !$toTimestamp && $from === Specification::ONE_DAY_AGO => 'Last 24 Hours',
            !$toTimestamp && $from === Specification::ONE_WEEK_AGO => 'Last 7 Days',
            !$toTimestamp && $from === Specification::ONE_MONTH_AGO => 'Last 30 Days',
            !$toTimestamp => \sprintf('From %s to now', $specification->toArray()['from']->format('Y-m-d H:i:s')),
            default => \sprintf('From %s to %s', $specification->toArray()['from']->format('Y-m-d H:i:s'), $toTimestamp->format('Y-m-d H:i:s')),
        };
        $waitTime = $snapshot->averageWaitTime();
        $handlingTime = $snapshot->averageHandlingTime();
        $title = 'Statistics';

        if ($tag = $input->getOption('tag')) {
            $title .= " (tagged: {$tag})";
        }

        $table = $io->createTable()
            ->setHorizontal()
            ->setHeaderTitle($title)
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
                $specification->toArray()['transport'] ?? \implode(', ', $this->transportMonitor->names()),
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
}
