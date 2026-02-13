<?php

/**
 * Este archivo funciona como intermediario para cargar el autoload
 * 
 * No debe ser modificado o eliminado
 *
 * @author Martin Sanchez
 * 
 * @version 1.0
 */

declare(strict_types=1);

//use DI\ContainerBuilder;
//use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';


//$dotenv = Dotenv::createImmutable(__DIR__);
//$dotenv->safeLoad();
//
//$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'MAIL_HOST', 'MAIL_USER', 'MAIL_PASSWORD']);
//
//$isDev = ($_ENV['APP_ENV'] ?? 'prod') === 'dev';
//
//$builder = new ContainerBuilder();
//
//
//if (!$isDev) {
//    $builder->enableCompilation(__DIR__ . '/tmp/di_cache');
//    $builder->writeProxiesToFile(true, __DIR__ . '/tmp/di_proxies');
//}
//
//$settings = require_once __DIR__ . '/config/settings.php';
//$settings($builder);
//
//$definitions = require_once __DIR__ . '/config/definitions.php';
//$definitions($builder);
//
//$services = require_once __DIR__ . '/config/services.php';
//$services($builder);
//
//$repositories = require_once __DIR__ . '/config/repositories.php';
//$repositories($builder);
//
//
//return $builder->build();
