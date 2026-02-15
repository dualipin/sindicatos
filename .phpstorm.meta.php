<?php

namespace PHPSTORM_META {
    // Esto indica que el método get() de la interfaz PSR-11
    // devuelve un objeto del tipo pasado en el primer argumento.
    override(\Psr\Container\ContainerInterface::get(0), map(['' => '@']));

    // Si usa directamente la clase de PHP-DI, añada también esta línea.
    override(\DI\Container::get(0), map(['' => '@']));
}