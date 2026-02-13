<?php

namespace App;

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Psr\Container\ContainerInterface;

final class Bootstrap
{
    private static ?ContainerInterface $instance = null;

    public static function buildContainer(): ContainerInterface
    {
        if (self::$instance === null) {
            self::$instance = self::build();
        }
        return self::$instance;
    }

    private static function build(): ContainerInterface
    {

        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->safeLoad();
        $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'MAIL_HOST', 'MAIL_USER', 'MAIL_PASSWORD']);

        $isDev = ($_ENV['APP_ENV'] ?? 'prod') === 'dev';
        $builder = new ContainerBuilder();

        if (!$isDev) {
            $builder->enableCompilation(__DIR__ . '/../tmp/di_cache');
            $builder->writeProxiesToFile(true, __DIR__ . '/../tmp/di_proxies');
        }

        (require_once __DIR__ . '/../config/settings.php')($builder);
        (require_once __DIR__ . '/../config/definitions.php')($builder);
        (require_once __DIR__ . '/../config/services.php')($builder);
        (require_once __DIR__ . '/../config/repositories.php')($builder);

        return $builder->build();
    }
}