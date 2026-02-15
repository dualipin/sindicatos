<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Http\Controller\Publicacion\PublicacionController;
use App\Infrastructure\Session\SessionManager;
use App\Module\Usuario\Service\AutenticacionService;

require_once __DIR__ . "/../../../bootstrap.php";

$container = Bootstrap::buildContainer();

/** @var SessionManager $session */
$session = $container->get(SessionManager::class);
$session->start();

/** @var AutenticacionService $auth */
$auth = $container->get(AutenticacionService::class);
$auth->requireLogin();

/** @var PublicacionController $controller */
$controller = $container->get(PublicacionController::class);
$controller->avisos();
