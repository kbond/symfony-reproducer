<?php

namespace App;

use App\Message\MessageA;
use App\Message\MessageB;
use App\Message\MultiMessage;
use Symfony\Component\Console\Messenger\RunCommandMessage;
use Symfony\Component\HttpClient\Messenger\PingWebhookMessage;
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
            ->add(RecurringMessage::every(10, new MessageA('always fails', throw: true)))
            ->add(RecurringMessage::cron('#midnight', new MessageB('from schedule 3'))->withJitter())
            ->add(RecurringMessage::cron('#midnight', new RunCommandMessage('messenger:monitor:purge --exclude-schedules'))->withJitter())
            ->add(RecurringMessage::cron('#midnight', new RunCommandMessage('messenger:monitor:schedule:purge --remove-orphans'))->withJitter())
            ->add(RecurringMessage::cron('#midnight', new PingWebhookMessage('GET', 'https://symfony.com'))->withJitter())
            ->add(RecurringMessage::cron('#midnight', new MultiMessage()))
        ;
    }
}
