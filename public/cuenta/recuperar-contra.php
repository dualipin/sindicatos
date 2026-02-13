<?php
declare(strict_types=1);


use App\Bootstrap;
use App\Infrastructure\Session\SessionManager;
use App\Infrastructure\Templating\RendererInterface;


$container = Bootstrap::buildContainer();

$session = $container->get(SessionManager::class);
$session->start();

$renderer = $container->get(RendererInterface::class);


echo 'Listo';