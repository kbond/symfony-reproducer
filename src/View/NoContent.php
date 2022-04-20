<?php

namespace App\View;

use App\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class NoContent extends View
{
    protected function __construct()
    {
    }

    /**
     * @internal
     */
    public function __invoke(): Response
    {
        return $this->manipulate(new Response(null, 204));
    }
}
