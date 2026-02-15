<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Http\Response\Redirector;
use App\Infrastructure\Session\SessionManager;
use App\Module\Usuario\Service\AutenticacionService;

require_once __DIR__ . "/../../bootstrap.php";

$container = Bootstrap::buildContainer();

$session = $container->get(SessionManager::class);
$session->start();

$auth = $container->get(AutenticacionService::class);
$auth->logout();

$redirector = $container->get(Redirector::class);
$redirector->to("/cuenta/login.php")->send();
