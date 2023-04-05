<?php

namespace App\Messenger\Monitor\Command;

use App\Messenger\Monitor\Stamp\MonitorStamp;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Envelope;
use function Zenstruck\collect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
#[AsCommand(
    name: 'messenger:monitor:transports',
    description: 'Display messenger transport status',
)]
final class TransportsCommand extends MonitorCommand
{
    use Refreshable;

    protected function configure(): void
    {
        $this
            ->addArgument('transport', InputArgument::OPTIONAL, 'The transport to list queued messages for', null, $this->transportMonitor->names())
            ->addOption('countable', null, InputOption::VALUE_NONE, 'Only show "countable" transports')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'The maximum number of messages to display', 10)
        ;
    }

    private static function title(): string
    {
        return 'Monitor Messenger Transports';
    }

    private function render(SymfonyStyle $io, InputInterface $input): int
    {
        $transports = $this->transportMonitor;

        if ($input->getOption('countable')) {
            $transports = $transports->countable();
        }

        if (!$transport = $input->getArgument('transport')) {
            $this->renderTransportStatus($io, $transports);

            return $transports->count() ? self::SUCCESS : self::FAILURE;
        }

        $status = $transports->get($transport);

        if (!$status->isListable()) {
            throw new \RuntimeException(sprintf('Transport "%s" is not listable', $transport));
        }

        $page = collect($status)->paginate(limit: $input->getOption('limit'));
        $table = $io->createTable()
            ->setHeaderTitle(\sprintf('Transport "%s"', $transport))
            ->setHeaders(['Message Type', 'Received At'])
            ->setFooterTitle(\sprintf('Total: %d', $page->totalCount()))
        ;

        if (!$page->totalCount()) {
            $table->addRow([new TableCell('<comment>[!] No queued messages.</comment>', [
                'colspan' => 2,
                'style' => new TableCellStyle(['align' => 'center']),
            ])]);
            $table->render();

            return self::SUCCESS;
        }

        foreach ($page as $envelope) {
            /** @var Envelope $envelope */
            $table->addRow([
                $envelope->getMessage()::class,
                $envelope->last(MonitorStamp::class)?->dispatchedAt()?->format('Y-m-d H:i:s') ?? '<comment>unknown</comment>',
            ]);
        }

        $table->render();

        return self::SUCCESS;
    }
}
