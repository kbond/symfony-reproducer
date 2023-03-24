<?php

namespace App;

use App\Message\MyMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsSchedule]
final class Schedule1 implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
//            ->add(RecurringMessage::cron('10 10 5 2 0', new \stdClass()))
            ->add(RecurringMessage::cron('* * * * *', new MyMessage()))
        ;
    }

}
