<?php

namespace Zenstruck\FormRequest\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Zenstruck\FormRequest\Exception\ValidationFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ValidationFailedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [ExceptionEvent::class => 'onException'];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ValidationFailed) {
            return;
        }

        $request = $event->getRequest();

        // todo support other serialization formats
        if ('json' !== $request->getPreferredFormat()) {
            return;
        }

        // todo serialize if available
        $event->setResponse(new JsonResponse($exception, Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
