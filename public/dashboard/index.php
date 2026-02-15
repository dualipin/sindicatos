<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Http\Controller\Dashboard\DashboardController;
use App\Infrastructure\Session\SessionManager;
use App\Module\Usuario\Service\AutenticacionService;

require_once __DIR__ . "/../../bootstrap.php";

$container = Bootstrap::buildContainer();

/** @var SessionManager $session */
$session = $container->get(SessionManager::class);
$session->start();

/** @var AutenticacionService $auth */
$auth = $container->get(AutenticacionService::class);
$auth->requireLogin();

/** @var DashboardController $controller */

// Redirigir a la nueva ubicación del dashboard
header("Location: /portal/dashboard/index.php");
exit();
