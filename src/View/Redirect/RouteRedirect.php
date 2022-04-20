<?php

namespace App\View\Redirect;

use App\View\Redirect;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class RouteRedirect extends Redirect
{
    protected function __construct(
        private string $name,
        private array $parameters = [],
        private $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ) {
        parent::__construct();
    }

    /**
     * @internal
     */
    public function __invoke(Request $request, ContainerInterface $container, ?Response $response = null): Response
    {
        return parent::__invoke($request, $container, new RedirectResponse(
            $container->get(UrlGeneratorInterface::class)->generate($this->name, $this->parameters, $this->referenceType)
        ));
    }

    public function absolute(): self
    {
        $this->referenceType = UrlGeneratorInterface::ABSOLUTE_URL;

        return $this;
    }
}
