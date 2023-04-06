<?php

namespace App\Messenger\Monitor\Scheduler;

use App\Messenger\Monitor\Stamp\ScheduleId;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

/**
 * TODO - remove if https://github.com/symfony/symfony/pull/49838 and https://github.com/symfony/symfony/pull/49865 are merged
 * TODO - add compiler pass to decorate schedule providers
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AutoIdScheduleProvider implements ScheduleProviderInterface
{
    public function __construct(private ScheduleProviderInterface $inner)
    {
    }

    public function getSchedule(): Schedule
    {
        $innerSchedule = $this->inner->getSchedule();
        $schedule = new Schedule();
        $ids = [];

        if ($state = $innerSchedule->getState()) {
            $schedule->stateful($state);
        }

        if ($lock = $innerSchedule->getLock()) {
            $schedule->lock($lock);
        }

        foreach ($innerSchedule->getRecurringMessages() as $message) {
            $schedule->add(ScheduleId::wrap($message));

            if (\in_array($id = $message->getMessage()->last(ScheduleId::class)->id, $ids, true)) {
                throw new \LogicException(\sprintf('Duplicate schedule ID "%s".', $id));
            }

            $ids[] = $id;
        }

        return $schedule;
    }
}
