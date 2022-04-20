<?php

namespace App\Controller;

use App\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class JsonController extends AbstractController
{
    #[Route('/json', name: 'app_json')]
    public function __invoke(): View
    {
        return View::json(['foo' => 'bar']);
    }
}
