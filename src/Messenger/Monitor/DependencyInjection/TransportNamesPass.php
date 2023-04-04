<?php

namespace App\Messenger\Monitor\DependencyInjection;

use App\Messenger\Monitor\Command\MessengerMonitorCommand;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class TransportNamesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $names = [];

        foreach ($container->findTaggedServiceIds('messenger.receiver') as $tags) {
            foreach ($tags as $tag) {
                $names[] = $tag['alias'] ?? null;
            }
        }

        $container->setParameter('zenstruck_messenger_monitor.transport_names', \array_filter($names));
    }
}
