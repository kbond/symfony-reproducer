<?php

namespace App;

use App\Message\MessageA;
use App\Message\MessageB;
use Symfony\Component\Messenger\Message\RedispatchMessage;
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
            ->add(RecurringMessage::every(6, new MessageA('from schedule 1'))->withJitter(1))
            ->add(RecurringMessage::every(20, new RedispatchMessage(new MessageA('from schedule 1'), 'async'))->withJitter(1))
            ->add(RecurringMessage::every(10, new MessageA('from schedule 2', throw: true)))
            ->add(RecurringMessage::cron('#midnight', new MessageB('from schedule 3'))->withJitter())
        ;
    }
}
