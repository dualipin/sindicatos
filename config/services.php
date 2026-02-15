<?php
declare(strict_types=1);
use DI\ContainerBuilder;
return function (ContainerBuilder $container) {
    $container->addDefinitions([
        App\Module\Usuario\Service\ImportUsersService::class => DI\autowire(),
    ]);
};
