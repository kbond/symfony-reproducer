<?php

namespace App;

use App\Message\MessageA;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsSchedule]
final class ScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(RecurringMessage::every(6, new MessageA('from schedule 1')))
            ->add(RecurringMessage::every(10, new MessageA('from schedule 2')))
        ;
    }
}
