<?php

namespace App\EventListener;

use App\View;
use App\View\Json;
use App\View\NoContent;
use App\View\Redirect\RouteRedirect;
use App\View\Redirect\UrlRedirect;
use App\View\Serialized;
use App\View\Template;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

        $event->setResponse(match(true) {
            $view instanceof UrlRedirect => $view($event->getRequest()),
            $view instanceof RouteRedirect => $view($event->getRequest(), $this->container->get(UrlGeneratorInterface::class)),
            $view instanceof Template => $this->createTemplateResponse($view),
            $view instanceof Json => $this->createJsonResponse($view),
            $view instanceof Serialized => $this->createSerializedResponse($event->getRequest(), $view),
            $view instanceof NoContent => $view(),
            default => throw new \LogicException(\sprintf('Unable to create response for "%s".', $view::class)),
        });
    }

    private function createSerializedResponse(Request $request, Serialized $view): Response
    {
        if (!$this->container->has(SerializerInterface::class)) {
            throw new \LogicException(\sprintf('The serializer is required to use "%s". Try running "composer require serializer".', Serialized::class));
        }

        return $view($request, $this->container->get(SerializerInterface::class));
    }

    private function createJsonResponse(Json $json): JsonResponse
    {
        return $json($this->container->has(SerializerInterface::class) ? $this->container->get(SerializerInterface::class) : null);
    }

    private function createTemplateResponse(Template $template): Response
    {
        if (!$this->container->has(Environment::class)) {
            throw new \LogicException(\sprintf('Twig is required to use "%s". Try running "composer require twig".', Template::class));
        }

        return $template($this->container->get(Environment::class));
    }
}
