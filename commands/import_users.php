<?php

declare(strict_types=1);

echo "Cargando autoloader...\n";
require_once __DIR__ . "/../bootstrap.php";

use App\Bootstrap;
use App\Module\Usuario\Service\ImportUsersService;

error_reporting(E_ALL);
ini_set("display_errors", "1");

try {
    echo "Construyendo contenedor...\n";
    $container = Bootstrap::buildContainer();
    echo "Obteniendo servicio de importación...\n";
    $importService = $container->get(ImportUsersService::class);

    $csvPath = __DIR__ . "/../data/usuarios_sindicato_1.csv";
    $sindicatoId = 1;

    echo "Iniciando importación desde $csvPath...\n";
    $importService->import($csvPath, $sindicatoId);
    echo "Importación completada con éxito.\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " LINE: " . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
