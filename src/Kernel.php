<?php

declare(strict_types=1);

namespace App;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
        ];
    }

    /**
     * @phpstan-ignore-next-line
     */
    private function configureContainer(ContainerConfigurator $container): void
    {
        $configDir = $this->getConfigDir();

        $container->import($configDir . '/services.php');

        if ($this->environment === 'test') {
            $container->import($configDir . '/services_test.php');
        }
    }
}
