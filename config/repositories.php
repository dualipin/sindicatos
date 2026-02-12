<?php

declare(strict_types=1);

use DI\ContainerBuilder;

use function DI\autowire;

return function (ContainerBuilder $container) {
    $container->addDefinitions([
        // \App\Domain\Sindicato\ConfiguracionSindicatoRepositoryInterface::class => autowire(
        //     \App\Infrastructure\Repositories\Sindicato\ConfiguracionSindicatoRepository::class
        // )
    ]);
};
