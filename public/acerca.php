<?php

use App\Bootstrap;
use App\Infrastructure\Session\SessionManager;
use App\Infrastructure\Templating\RendererInterface;
use App\Shared\View\LandingViewContextProvider;
use DI\Container;

require_once __DIR__ . '/../bootstrap.php';

$container = Bootstrap::buildContainer();


/** @var RendererInterface $renderer */
$renderer = $container->get(RendererInterface::class);

/** @var SessionManager $session */
$session = $container->get(SessionManager::class);
$session->start();

/** @var LandingViewContextProvider $landingContext */
$landingContext = $container->get(LandingViewContextProvider::class);

$data = [
    ...$landingContext->get(),
];

$renderer->render(__DIR__ . '/acerca.latte', $data);