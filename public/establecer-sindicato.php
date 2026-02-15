<?php

use App\Bootstrap;
use App\Http\Controller\Sindicatos\EstablecerSindicatoController;

require_once __DIR__ . '/../bootstrap.php';

$container = Bootstrap::buildContainer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$controller = $container->get(EstablecerSindicatoController::class);
$controller->handle($_POST);