<?php

declare(strict_types=1);


use App\Http\Controller\Auth\LoginController;
use App\Infrastructure\Session\SessionManager;
use App\Infrastructure\Templating\RendererInterface;
use App\Shared\View\SindicatoViewContextProvider;
use DI\Container;

/** @var Container $container */
$container = require_once __DIR__ . '/../../bootstrap.php';

/** @var SessionManager $session */
$session = $container->get(SessionManager::class);
$session->start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    /** @var LoginController $controller */
    $controller = $container->get(LoginController::class);

    $controller->handle($email, $password);
}

/** @var RendererInterface $renderer */
$renderer = $container->get(RendererInterface::class);

/** @var SindicatoViewContextProvider $sindicatoProvider */
$sindicatoProvider = $container->get(SindicatoViewContextProvider::class);

$data = [
    'email' => '',
    'error' => '',
    'redirect' => '',
    ...$sindicatoProvider->get()
];

$renderer->render(__DIR__ . '/login.latte', $data);