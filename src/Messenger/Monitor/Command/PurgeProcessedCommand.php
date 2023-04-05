<?php

namespace App\Messenger\Monitor\Command;

use App\Messenger\Monitor\Storage\Specification;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
#[AsCommand(
    name: 'messenger:monitor:purge-processed',
    description: 'Purge processed messages',
)]
final class PurgeProcessedCommand extends ProcessedFilterCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->addOption('older-than', null, InputOption::VALUE_REQUIRED, 'The start date of the statistics', Specification::ONE_MONTH_AGO, Specification::DATE_PRESETS)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $specification = self::createSpecification(
            $input,
            to: $input->getOption('older-than'),
        );
        $count = $this->storage->count($specification);

        $io->title('Purge Processed Messages');
        $io->comment(\sprintf('Older than <info>%s</info>', $specification->toArray()['to']->format('Y-m-d H:i:s')));

        if (!$count) {
            $io->warning('No processed messages found.');

            return self::SUCCESS;
        }

        if ($input->isInteractive() && !$io->confirm(\sprintf('Are you sure you want to purge <comment>%s</comment> processed messages?', $count), false)) {
            $io->warning('Aborted.');

            return self::SUCCESS;
        }

        $io->success(\sprintf('Purged %s processed messages.', $this->storage->purge($specification)));

        return self::SUCCESS;
    }
}
