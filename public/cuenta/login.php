<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Http\Controller\Auth\LoginController;
use App\Infrastructure\Session\SessionManager;

require_once __DIR__ . "/../../bootstrap.php";

$container = Bootstrap::buildContainer();

/** @var SessionManager $session */
$session = $container->get(SessionManager::class);
$session->start();

/** @var LoginController $controller */
$controller = $container->get(LoginController::class);

$redirectTo = $_GET["redirect_to"] ?? ($_POST["redirect"] ?? "/portal/dashboard");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $controller->login(
        (string) $email,
        (string) $password,
        (string) $redirectTo,
    );
} else {
    $error = $_GET["error"] ?? null;
    $controller->showLoginForm((string) $redirectTo, (string) $error);
}
