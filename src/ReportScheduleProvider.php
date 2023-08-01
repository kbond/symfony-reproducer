<?php

namespace App;

use App\Message\MessageB;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[AsSchedule('report')]
final class ReportScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(RecurringMessage::cron('#midnight', new MessageB('from schedule 3'))->withJitter())
        ;
    }
}
