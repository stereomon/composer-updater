<?php

declare(strict_types=1);

namespace App\DependencyUpdater;

use App\Shared\Transfer\ErrorTransfer;
use App\Shared\Transfer\UpdateDependencyRequestTransfer;
use App\Shared\Transfer\UpdateDependencyResponseTransfer;
use Psr\Log\LoggerInterface;

class DependencyUpdaterService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ComposerFileHandler $composerFileHandler
    ) {
    }

    public function updateDependency(
        UpdateDependencyRequestTransfer $updateDependencyRequestTransfer
    ): UpdateDependencyResponseTransfer {
        $packageName = $updateDependencyRequestTransfer->getPackageName();

        if (empty(trim($packageName))) {
            $this->logger->warning('Empty package name provided');

            return new UpdateDependencyResponseTransfer(
                false,
                [],
                '',
                [new ErrorTransfer('Package name cannot be empty')]
            );
        }

        $expectedVersion = $updateDependencyRequestTransfer->getExpectedVersion();

        if (empty(trim($expectedVersion))) {
            $this->logger->warning('Empty expected version provided');

            return new UpdateDependencyResponseTransfer(
                false,
                [],
                '',
                [new ErrorTransfer('Expected version cannot be empty')]
            );
        }

        $moduleNames = $updateDependencyRequestTransfer->getModuleNames();

        if (empty($moduleNames)) {
            $this->logger->warning('No module names provided');

            return new UpdateDependencyResponseTransfer(
                false,
                [],
                '',
                [new ErrorTransfer('At least one module name must be provided')]
            );
        }

        $updatedModules = [];

        foreach ($moduleNames as $moduleName) {
            if (empty(trim($moduleName))) {
                $this->logger->warning('Empty module name encountered, skipping');
                continue;
            }

            $composerPath = getcwd() . '/Bundles/' . $moduleName . '/composer.json';

            if (!$this->composerFileHandler->composerFileExists($composerPath)) {
                $this->logger->warning(sprintf('Composer file not found: %s', $composerPath));
                continue;
            }

            $currentVersion = $this->composerFileHandler->getPackageVersion($composerPath, $packageName);

            if ($currentVersion === $expectedVersion) {
                continue;
            }

            if ($packageName === 'spryker/transfer' && $currentVersion === null) {
                if (!$this->composerFileHandler->createDependencyJson($moduleName)) {
                    $this->logger->error(sprintf('Failed to create dependency.json for module: %s', $moduleName));
                    continue;
                }
            }

            if (!$this->composerFileHandler->updatePackageVersion($composerPath, $packageName, $expectedVersion)) {
                $this->logger->error(sprintf('Failed to update package version in: %s', $composerPath));
                continue;
            }

            $updatedModules[] = $moduleName;
        }

        $message = sprintf(
            'Updated %d modules with %s version %s',
            count($updatedModules),
            $packageName,
            $expectedVersion
        );

        return new UpdateDependencyResponseTransfer(true, $updatedModules, $message);
    }
}
