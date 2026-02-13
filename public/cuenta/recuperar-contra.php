<?php
declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use App\Bootstrap;
use App\Infrastructure\Session\SessionManager;
use App\Infrastructure\Templating\RendererInterface;
use App\Shared\View\SindicatoViewContextProvider;


$container = Bootstrap::buildContainer();

$session = $container->get(SessionManager::class);
$session->start();

$sindicatoActual = $container->get(SindicatoViewContextProvider::class);

$renderer = $container->get(RendererInterface::class);
$renderer->render(__DIR__ . '/recuperar-contra.latte', [
    ...$sindicatoActual->get()
]);