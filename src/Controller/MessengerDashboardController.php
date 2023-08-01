<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Zenstruck\Messenger\Monitor\Controller\MessengerMonitorController;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[Route('/messenger')]
final class MessengerDashboardController extends MessengerMonitorController
{
}
