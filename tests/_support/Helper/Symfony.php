<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

use App\Kernel;
use Codeception\Module;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Symfony extends Module
{
    private ?ContainerInterface $container = null;
    private array $mocks = [];

    public function _initialize(): void
    {
        $kernel = new Kernel('test', true);
        $kernel->boot();
        $this->container = $kernel->getContainer();
    }

    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $this->_initialize();
        }
        
        if ($this->container === null) {
            throw new \RuntimeException('Container could not be initialized');
        }
        
        return $this->container;
    }

    public function get(string $serviceId): object
    {
        if (isset($this->mocks[$serviceId])) {
            return $this->mocks[$serviceId];
        }
        
        return $this->getContainer()->get($serviceId);
    }

    public function set(string $serviceId, object $service): void
    {
        $this->mocks[$serviceId] = $service;
    }

    public function clearMocks(): void
    {
        $this->mocks = [];
    }
}
