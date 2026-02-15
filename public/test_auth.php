<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Infrastructure\Session\SessionManager;
use App\Module\Usuario\Service\AutenticacionService;

require_once __DIR__ . "/../bootstrap.php";

$container = Bootstrap::buildContainer();

$session = $container->get(SessionManager::class);
$session->start();

$auth = $container->get(AutenticacionService::class);
$auth->requireLogin();

echo "<h1>Acceso Permitido</h1>";
echo "<p>Bienvenido, " .
    htmlspecialchars($auth->getUsuarioAutenticado()->correo) .
    "</p>";
echo "<p><a href='/cuenta/logout.php'>Cerrar Sesión</a></p>";
