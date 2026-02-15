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

// Si no hay integrantes del comite en el contexto (BD vacia o entorno de demo),
// agregamos un fallback estatico para que la vista siempre muestre el listado.
$contextoAcerca = $data['contextoAcerca'] ?? null;
$comiteExistente = $contextoAcerca?->comite ?? ($data['comite'] ?? null);
if (empty($comiteExistente)) {
    $sindicatoId = $data['sindicatoActual']?->id ?? 1;
    $fallbackComite = [
        ['id' => null, 'sindicatoId' => $sindicatoId, 'puesto' => 'Secretario General', 'nombre' => 'M.I.D.S. Luiz Sosa Castro', 'foto' => null, 'biografia' => null, 'activo' => true],
        ['id' => null, 'sindicatoId' => $sindicatoId, 'puesto' => 'Secretario General Suplente', 'nombre' => 'Ing. Gilberto Enrique Ascencio Suárez', 'foto' => null, 'biografia' => null, 'activo' => true],
        ['id' => null, 'sindicatoId' => $sindicatoId, 'puesto' => 'Secretario de Organización', 'nombre' => 'Ing. Walberto Cornelio González', 'foto' => null, 'biografia' => null, 'activo' => true],
        ['id' => null, 'sindicatoId' => $sindicatoId, 'puesto' => 'Secretario de Trabajos y Conflictos', 'nombre' => 'Mtro. Jorge Alberto Vargas García', 'foto' => null, 'biografia' => null, 'activo' => true],
        ['id' => null, 'sindicatoId' => $sindicatoId, 'puesto' => 'Secretario de Finanzas', 'nombre' => 'Ing. Jesús Antonio López Hernández', 'foto' => null, 'biografia' => null, 'activo' => true],
        ['id' => null, 'sindicatoId' => $sindicatoId, 'puesto' => 'Secretario de Actas y Acuerdos', 'nombre' => 'Roberto Morales Morales', 'foto' => null, 'biografia' => null, 'activo' => true],
        ['id' => null, 'sindicatoId' => $sindicatoId, 'puesto' => 'Presidente de la Comisión de Honor y Justicia', 'nombre' => 'Mtro. Humberto Rincón Rincón', 'foto' => null, 'biografia' => null, 'activo' => true],
    ];

    if ($contextoAcerca instanceof \stdClass) {
        $contextoAcerca->comite = $fallbackComite;
        $data['contextoAcerca'] = $contextoAcerca;
    } else {
        $data['contextoAcerca'] = (object) ['datos' => [], 'comite' => $fallbackComite];
    }

    // mantener tambien indice top-level `comite` por compatibilidad
    $data['comite'] = $fallbackComite;
}

$renderer->render(__DIR__ . '/acerca.latte', $data);