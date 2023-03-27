<?php

namespace App;

use App\Message\MessageA;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
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
            ->add(RecurringMessage::every('5 seconds', new MessageA('from schedule')))
        ;
    }
}
