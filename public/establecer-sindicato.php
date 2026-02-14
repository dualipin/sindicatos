<?php

use App\Bootstrap;
use App\Http\Response\Redirector;
use App\Shared\Context\TenantContext;

require_once __DIR__ . '/../bootstrap.php';

$container = Bootstrap::buildContainer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$context = $container->get(TenantContext::class);

$id = $_POST['id'];

$context->setSyndicateId($id);

$redirect = $container->get(Redirector::class);

$redirect->to('/', ['id' => $id])->send();