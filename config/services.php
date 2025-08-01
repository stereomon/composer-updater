<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    // Register all services in src/
    $services->load('App\\', '../src/')
        ->exclude([
            '../src/DependencyInjection/',
            '../src/Entity/',
            '../src/Kernel.php',
            '../src/Shared/Transfer/',
        ]);

    // Configure Monolog
    $services->set('monolog.handler.stdout', StreamHandler::class)
        ->args(['php://stdout', Logger::INFO]);

    $services->set(LoggerInterface::class, Logger::class)
        ->args([
            'app',
            [service('monolog.handler.stdout')]
        ]);
};
