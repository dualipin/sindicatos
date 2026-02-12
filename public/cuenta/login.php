<?php

declare(strict_types=1);


use App\Infrastructure\Session\SessionManager;
use DI\Container;

/** @var Container $container */
$container = require_once __DIR__ . '/../../bootstrap.php';

/** @var SessionManager $session */
$session = $container->get(SessionManager::class);
$session->start();

