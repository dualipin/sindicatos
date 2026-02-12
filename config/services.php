<?php
declare(strict_types=1);
use DI\ContainerBuilder;
return function (ContainerBuilder $container) {
    $container->addDefinitions([
        // App\Controller\LandingController::class => DI\autowire(),
        // App\UseCases\Sindicato\ObtenerContextoSindicato::class => DI\autowire(),
        // App\Controller\AcercaController::class => DI\autowire(),
        // App\UseCases\Acerca\ObtenerContextoAcerca::class => DI\autowire(),
    ]);
};
