<?php

declare(strict_types=1);

namespace Tests\Support\Helper;

use Codeception\Module;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class Console extends Module
{
    public function createCommandTester(Command $command): CommandTester
    {
        $application = new Application();
        $application->add($command);
        
        return new CommandTester($command);
    }

    public function getCommandTester(string|Command $command): CommandTester
    {
        if (is_string($command)) {
            /** @var \Tests\Support\Helper\Symfony $symfonyHelper */
            $symfonyHelper = $this->getModule('Tests\Support\Helper\Symfony');
            $command = $symfonyHelper->get($command);
        }

        return $this->createCommandTester($command);
    }
}
