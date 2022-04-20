<?php

namespace App\Controller;

use App\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class RedirectController extends AbstractController
{
    #[Route('/redirect', name: 'app_redirect')]
    public function __invoke(): View
    {
        return View::redirectToRoute('app_homepage', ['foo' => 'bar'])
            ->withError(new TranslatableMessage('some error'))
            ->withInfo('some info')
        ;
    }
}
