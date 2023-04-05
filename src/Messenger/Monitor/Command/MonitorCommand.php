<?php

namespace App\Messenger\Monitor\Command;

use App\Messenger\Monitor\Statistics;
use App\Messenger\Monitor\Storage;
use App\Messenger\Monitor\TransportMonitor;
use App\Messenger\Monitor\WorkerMonitor;
use Symfony\Component\Console\Command\Command;

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
}
