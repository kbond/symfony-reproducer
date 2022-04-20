<?php

namespace App\EventListener;

use App\View;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Environment;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ViewSubscriber implements EventSubscriberInterface, ServiceSubscriberInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [ViewEvent::class => 'onKernelView'];
    }

    public static function getSubscribedServices(): array
    {
        return [
            UrlGeneratorInterface::class,
            '?'.Environment::class,
            '?'.SerializerInterface::class,
        ];
    }

    public function onKernelView(ViewEvent $event): void
    {
        $view = $event->getControllerResult();

        if (!$view instanceof View) {
            return;
        }

        $event->setResponse($view($event->getRequest(), $this->container));
    }
}
