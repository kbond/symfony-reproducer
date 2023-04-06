<?php

namespace App;

use App\Message\MessageA;
use App\Messenger\Monitor\Stamp\ScheduleId;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule]
class Kernel extends BaseKernel implements ScheduleProviderInterface
{
    use MicroKernelTrait;

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(RecurringMessage::every('1 seconds', new MessageA('from schedule')))
            ->add(RecurringMessage::every('1 seconds', Envelope::wrap(new MessageA('with-id'), [new ScheduleId('my-id')])))
        ;
    }
}
