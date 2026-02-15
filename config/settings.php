<?php


use App\Infrastructure\Config\AppConfig;
use App\Infrastructure\Config\DatabaseConfig;
use App\Infrastructure\Config\MailerConfig;
use App\Infrastructure\Config\UploadConfig;
use DI\ContainerBuilder;

return function (ContainerBuilder $container) {
    $container->addDefinitions([
        AppConfig::class => function () {
            return new AppConfig(
                isDev: ($_ENV['APP_ENV'] ?? 'prod') === 'dev',
                baseUrl: $_ENV['BASE_URL'] ?? '',
                database: new DatabaseConfig(
                    host: $_ENV['DB_HOST'],
                    database: $_ENV['DB_NAME'],
                    user: $_ENV['DB_USER'],
                    password: $_ENV['DB_PASS'],
                    port: (int) ($_ENV['DB_PORT'] ?? 3306),
                    charset: 'utf8mb4'
                ),
                mailer: new MailerConfig(
                    host: $_ENV['MAIL_HOST'],
                    user: $_ENV['MAIL_USER'],
                    password: $_ENV['MAIL_PASSWORD'],
                    port: (int) ($_ENV['MAIL_PORT'] ?? 465),
                    charset: 'UTF-8'
                ),
                upload: new UploadConfig(
                    publicUrl: 'uploads',
                    publicDir: __DIR__ . '/../public/uploads',
                    privateDir: __DIR__ . '/../uploads',
                )
            );
        }
    ]);
};
