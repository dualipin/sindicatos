<?php

declare(strict_types=1);



use App\Http\Response\Redirector;
use App\Infrastructure\Config\AppConfig;
use App\Infrastructure\Templating\LatteRenderer;
use App\Infrastructure\Templating\LatteExtensions;
use App\Infrastructure\Templating\RendererInterface;
use DI\Container;
use DI\ContainerBuilder;
use Latte\Engine;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

use function DI\autowire;
use function DI\factory;


return function (ContainerBuilder $container) {
    $container->addDefinitions([
        PDO::class => function (Container $container): PDO {
            /** @var AppConfig $settings */
            $settings = $container->get(AppConfig::class);

            $dbSettings = $settings->database;
            $host = $dbSettings->host;
            $db = $dbSettings->database;
            $user = $dbSettings->user;
            $pass = $dbSettings->password;
            $port = $dbSettings->port;
            $charset = $dbSettings->charset;

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

            return new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, // Importante para seguridad real en MySQL
            ]);
        },

        Engine::class => function (LatteExtensions $templateExtensions, Container $container): Engine {
            $settings = $container->get(AppConfig::class);
            $isDev = $settings->isDev;
            $cacheDir = __DIR__ . '/../tmp/latte';

            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0775, true);
            }

            $latte = new Engine();

            $latte->addExtension($templateExtensions);

            $latte->setTempDirectory($cacheDir);
            $latte->setAutoRefresh($isDev);

            return $latte;
        },

        RendererInterface::class => autowire(LatteRenderer::class),
        Redirector::class => autowire(Redirector::class),

        LoggerInterface::class => factory(function (Container $container) {
            $logDir = __DIR__ . '/../logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0775, true);
            }

            $logger = new Logger('app_logger');
            $logger->pushHandler(new StreamHandler($logDir . '/app.log', Level::Debug));

            return $logger;
        }),
        // MAILER
        PHPMailer::class => function (Container $container): PHPMailer {
            $settings = $container->get(AppConfig::class);
            $mailerSettings = $settings->mailer;
            $mail = new PHPMailer(true);

            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = $mailerSettings->host;
            $mail->SMTPAuth = true;
            $mail->Username = $mailerSettings->user;
            $mail->Password = $mailerSettings->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = (int)$mailerSettings->port;
            $mail->CharSet = $mailerSettings->charset; // Importante para caracteres latinos

            // Configuraciones por defecto (opcional pero útil)
            $mail->setFrom($mailerSettings->user, $mailerSettings->user);
            $mail->Timeout = 10;

            return $mail;
        }
    ]);
};
