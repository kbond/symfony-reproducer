<?php

namespace App\View\Redirect;

use App\View\Redirect;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlRedirect extends Redirect
{
    protected function __construct(private string $url)
    {
        parent::__construct();
    }

    /**
     * @internal
     */
    public function __invoke(Request $request, ContainerInterface $container, ?Response $response = null): Response
    {
        return parent::__invoke($request, $container, new RedirectResponse($this->url));
    }
}
