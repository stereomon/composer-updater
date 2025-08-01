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
        ->autoconfigure()
        ->public(); // Make all services public for testing

    // Register all services in src/
    $services->load('App\\', '../src/')
        ->exclude([
            '../src/DependencyInjection/',
            '../src/Entity/',
            '../src/Kernel.php',
            '../src/Shared/Transfer/',
        ]);

    // Configure Monolog for testing
    $services->set('monolog.handler.stderr', StreamHandler::class)
        ->args(['php://stderr', Logger::DEBUG]);

    $services->set(LoggerInterface::class, Logger::class)
        ->args([
            'app_test',
            [service('monolog.handler.stderr')]
        ]);
};
