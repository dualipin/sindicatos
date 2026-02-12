<?php

declare(strict_types=1);

use App\Infrastructure\Session\SessionManager;
use App\Infrastructure\Templating\RendererInterface;
use App\Shared\View\LandingViewContextProvider;
use DI\Container;

/** @var Container $container */
$container = require_once __DIR__ . '/../bootstrap.php';

/** @var SessionManager $session */
$session = $container->get(SessionManager::class);
$session->start();

/** @var RendererInterface $renderer */
$renderer = $container->get(RendererInterface::class);

/** @var LandingViewContextProvider $landingContext */
$landingContext = $container->get(LandingViewContextProvider::class);


$renderer->render(__DIR__ . '/index.latte', $landingContext->get());