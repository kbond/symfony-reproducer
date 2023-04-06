<?php

namespace App\Messenger\Monitor\Command;

use App\Messenger\Monitor\Model\ProcessedMessage;
use App\Messenger\Monitor\Storage\Specification;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Zenstruck\collect;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
#[AsCommand(
    name: 'messenger:monitor:snapshot',
    description: 'Display a snapshot of processed messages'
)]
final class SnapshotCommand extends ProcessedFilterCommand
{
    use Refreshable;

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'The start date of the snapshot', Specification::ONE_DAY_AGO, Specification::DATE_PRESETS)
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'The end date of the snapshot')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'The maximum number of messages to display', 10)
        ;

        parent::configure();
    }

    private static function title(): string
    {
        return 'Processed Messages';
    }

    private function render(SymfonyStyle $io, InputInterface $input): int
    {
        $specification = self::createSpecification(
            $input,
            from: $input->getOption('from'),
            to: $input->getOption('to'),
        );

        $this->renderStatistics($io, $input, $specification);

        $page = collect($this->statistics->snapshot($specification)->messages())->paginate(limit: $input->getOption('limit'));

        if (!$page->count()) {
            return self::SUCCESS;
        }

        $table = $io->createTable()
            ->setHeaderTitle('Recently Processed Messages')
            ->setHeaders(['Type', 'Transport', 'Time in Queue', 'Time to Handle', 'Handled At', 'Tags'])
            ->setFooterTitle(\sprintf('Total: %d (Page %d of %d)', $page->totalCount(), $page->currentPage(), $page->lastPage()))
        ;

        foreach ($page as $message) {
            /** @var ProcessedMessage $message */

            $handledAt =$message->handledAt()->format('Y-m-d H:i:s');
            $table->addRow([
                $message->type()->shortName(),
                $message->transport(),
                Helper::formatTime($message->timeInQueue()),
                Helper::formatTime($message->timeToHandle()),
                $message->isFailure() ? \sprintf('<error>[!] %s</error>', $handledAt) : \sprintf('<info>%s</info>', $handledAt),
                $message->tags()->implode() ?? '(none)',
            ]);
        }

        $io->writeln('');
        $table->render();

        return self::SUCCESS;
    }
}
