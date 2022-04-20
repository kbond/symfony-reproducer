<?php

namespace App\View;

use App\View;
use App\View\Redirect\RouteRedirect;
use App\View\Redirect\UrlRedirect;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class Redirect extends View
{
    /** @var array<string,string[]> */
    private array $flashes = [];

    final public static function to(string $url): UrlRedirect
    {
        return new UrlRedirect($url);
    }

    final public static function toRoute(
        string $name,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): RouteRedirect {
        return new RouteRedirect($name, $parameters, $referenceType);
    }

    final public function permanent(): static
    {
        return $this->withStatus(301);
    }

    final public function withFlash(string $type, mixed $message): static
    {
        $this->flashes[$type][] = $message;

        return $this;
    }

    final public function withSuccess(mixed $message): static
    {
        return $this->withFlash('success', $message);
    }

    final public function withWarning(mixed $message): static
    {
        return $this->withFlash('warning', $message);
    }

    final public function withError(mixed $message): static
    {
        return $this->withFlash('error', $message);
    }

    final public function withInfo(mixed $message): static
    {
        return $this->withFlash('info', $message);
    }

    final protected function processFlashes(Request $request): void
    {
        foreach ($this->flashes as $type => $messages) {
            foreach ($messages as $message) {
                $request->getSession()->getFlashBag()->add($type, $message);
            }
        }
    }
}
