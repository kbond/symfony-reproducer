<?php

namespace App\View\Redirect;

use App\View\Redirect;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class UrlRedirect extends Redirect
{
    protected function __construct(private string $url)
    {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $this->processFlashes($request);

        return $this->manipulate(new RedirectResponse($this->url));
    }
}
