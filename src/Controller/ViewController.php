<?php

namespace App\Controller;

use App\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ViewController
{
    #[Route('/redirect')]
    public function redirect(): View
    {
        return View::redirectToRoute('app_homepage', ['foo' => 'bar'])
            ->withError(new TranslatableMessage('some error'))
            ->withInfo('some info')
        ;
    }

    #[Route('/json')]
    public function json(): View
    {
        return View::json(['foo' => 'bar']);
    }

    #[Route('/no-content', name: 'app_no_content')]
    public function noContent(): View
    {
        return View::noContent();
    }

    #[Route('/serialize')]
    public function serialize(): View
    {
        return View::serialize(['date' => new \DateTime()]);
    }
}
