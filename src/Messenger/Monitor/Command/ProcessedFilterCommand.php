<?php

namespace App\Messenger\Monitor\Command;

use App\Messenger\Monitor\Storage\Specification;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
abstract class ProcessedFilterCommand extends MonitorCommand
{
    protected function configure(): void
    {
        $this
            ->addOption('message-type', null, InputOption::VALUE_REQUIRED, 'Filter by message type')
            ->addOption('transport', null, InputOption::VALUE_REQUIRED, 'Filter by transport name', null, $this->transportMonitor->names())
            ->addOption('tag', null, InputOption::VALUE_REQUIRED, 'Filter by a tag')
        ;
    }

    protected static function createSpecification(
        InputInterface $input,
        string|\DateTimeImmutable|null $from = null,
        string|\DateTimeImmutable|null $to = null,
    ): Specification
    {
        return Specification::fromArray([
            'from' => $from,
            'to' => $to,
            'message_type' => $input->getOption('message-type'),
            'transport' => $input->getOption('transport'),
            'tag' => $input->getOption('tag'),
        ]);
    }
}
