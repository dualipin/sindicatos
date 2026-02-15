<?php

declare(strict_types=1);

use DI\ContainerBuilder;

use function DI\autowire;

return function (ContainerBuilder $container) {
    $container->addDefinitions([
        App\Module\Usuario\Repository\UsuarioRepository::class => DI\autowire(),
    ]);
};
