<?php

namespace App\Messenger\Monitor\Scheduler;

use Symfony\Component\Scheduler\Trigger\CronExpressionTrigger;
use Symfony\Component\Scheduler\Trigger\TriggerInterface;

/**
 * TODO - implement this
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AsScheduledCommand
{
    public readonly TriggerInterface $trigger;

    public function __construct(
        TriggerInterface|string $trigger,
    ) {
        if (!$trigger instanceof TriggerInterface) {
            $trigger = CronExpressionTrigger::fromSpec($trigger);
        }

        $this->trigger = $trigger;
    }
}
