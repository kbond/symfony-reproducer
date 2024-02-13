<?php

namespace App\Marmalade\EventListener;

use App\Marmalade\PageManager;
use Symfony\Component\DependencyInjection\Attribute\AutowireServiceClosure;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
#[AsEventListener]
final class MarmaladeRequestListener
{
    public function __construct(
        #[AutowireServiceClosure(PageManager::class)]
        private \Closure $pageManager,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        $attr = $event->getRequest()->attributes;

        if (!in_array($attr->get('_route'), ['marmalade_index', 'marmalade_page'], true)) {
            return;
        }

        $event->setResponse(
            new Response(($this->pageManager)()->render($attr->get('path'), $attr->get('_format')))
        );
    }
}
