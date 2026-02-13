<?php

declare(strict_types=1);

use App\Infrastructure\Session\SessionManager;
use App\Infrastructure\Templating\RendererInterface;
use App\Shared\View\LandingViewContextProvider;
use Psr\Container\ContainerInterface;


/** @var ContainerInterface $container */
$container = require_once __DIR__ . '/../bootstrap.php';


$session = $container->get(SessionManager::class);
$session->start();

$renderer = $container->get(RendererInterface::class);

$landingContext = $container->get(LandingViewContextProvider::class);

$renderer->render(__DIR__ . '/index.latte', $landingContext->get());
