<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Zenstruck\Messenger\Monitor\Controller\MonitorDashboardController;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[Route('/messenger', name: 'app_messenger')]
final class MessengerDashboardController extends MonitorDashboardController
{
}
