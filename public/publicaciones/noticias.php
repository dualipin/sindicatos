<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Infrastructure\Session\SessionManager;
use App\Infrastructure\Templating\RendererInterface;
use App\Module\Publicacion\View\PublicacionViewContextProvider;
use App\Shared\View\LandingViewContextProvider;

require_once __DIR__ . "/../../bootstrap.php";

$container = Bootstrap::buildContainer();

$session = $container->get(SessionManager::class);
$session->start();

$renderer = $container->get(RendererInterface::class);

$landingContext = $container->get(LandingViewContextProvider::class);
$publicacionContext = $container->get(PublicacionViewContextProvider::class);

$data = [
    ...$landingContext->get(),
    ...$publicacionContext->get(),
    "linkActivo" => "noticias",
];

$renderer->render(__DIR__ . "/noticias.latte", $data);
