<?php

declare(strict_types=1);

namespace App\Commands;

use App\Shared\Transfer\UpdateDependencyRequestTransfer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-dependency',
    description: 'Updates dependencies in module composer.json files',
)]
class UpdateDependencyCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('modules', InputArgument::REQUIRED, 'Comma-separated list of module names')
            ->addArgument('package-name', InputArgument::REQUIRED, 'The package name to update')
            ->addArgument('version', InputArgument::REQUIRED, 'The version to set')
            ->addArgument('description', InputArgument::OPTIONAL, 'Optional description for the update');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @phpstan-var string */
        $modulesArgument = $input->getArgument('modules');

        /** @phpstan-var string */
        $packageNameArgument = $input->getArgument('package-name');

        /** @phpstan-var string */
        $versionArgument = $input->getArgument('version');

        /** @phpstan-var ?string */
        $descriptionArgument = $input->getArgument('description');

        $moduleNames = array_map('trim', explode(',', $modulesArgument));
        $moduleNames = array_filter($moduleNames, fn($module) => !empty($module));

        if (empty($moduleNames)) {
            $io->error('At least one module name must be provided');

            return static::FAILURE;
        }

        $updateDependencyRequestTransfer = new UpdateDependencyRequestTransfer(
            $moduleNames,
            $packageNameArgument,
            $versionArgument,
            $descriptionArgument
        );

        $updateDependencyResponseTransfer = $this->appFacade->updateDependency($updateDependencyRequestTransfer);

        if (!$updateDependencyResponseTransfer->isSuccessful()) {
            $this->displayErrors($updateDependencyResponseTransfer->getErrors(), $io);

            return static::FAILURE;
        }

        $updateMessage = $updateDependencyResponseTransfer->getUpdateMessage();

        if ($updateMessage === null) {
            $io->error('No update message was generated');

            return self::FAILURE;
        }

        $updatedModules = $updateDependencyResponseTransfer->getUpdatedModules();

        $io->success($updateMessage);

        if (!empty($updatedModules)) {
            $io->section('Updated Modules:');
            $io->listing($updatedModules);
        }

        return self::SUCCESS;
    }
}
