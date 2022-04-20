<?php

namespace App\View\Redirect;

use App\View\Redirect;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
    }

    public function __invoke(Request $request, UrlGeneratorInterface $router): RedirectResponse
    {
        $this->processFlashes($request);

        return $this->manipulate(new RedirectResponse(
            $router->generate($this->name, $this->parameters, $this->referenceType)
        ));
    }

    public function absolute(): self
    {
        $this->referenceType = UrlGeneratorInterface::ABSOLUTE_URL;

        return $this;
    }
}
