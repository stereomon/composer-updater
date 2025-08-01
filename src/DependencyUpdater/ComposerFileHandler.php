<?php

declare(strict_types=1);

namespace App\DependencyUpdater;

use Psr\Log\LoggerInterface;

class ComposerFileHandler
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function composerFileExists(string $composerPath): bool
    {
        $realPath = realpath($composerPath);

        if ($realPath === false) {
            return false;
        }

        return file_exists($realPath) && is_readable($realPath);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function readComposerJson(string $composerPath): ?array
    {
        $realPath = realpath($composerPath);

        if ($realPath === false) {
            $this->logger->warning(sprintf('Invalid composer path: %s', $composerPath));

            return null;
        }

        if (!file_exists($realPath) || !is_readable($realPath)) {
            $this->logger->warning(sprintf('Composer file not readable: %s', $realPath));

            return null;
        }

        $content = file_get_contents($realPath);

        if ($content === false) {
            $this->logger->error(sprintf('Failed to read composer file: %s', $realPath));

            return null;
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error(sprintf('Invalid JSON in composer file: %s', json_last_error_msg()));

            return null;
        }

        if (!is_array($data)) {
            $this->logger->error('Composer file does not contain a valid JSON object');

            return null;
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $composerData
     */
    public function writeComposerJson(string $composerPath, array $composerData): bool
    {
        $realPath = realpath(dirname($composerPath));

        if ($realPath === false) {
            $this->logger->error(sprintf('Invalid directory path: %s', dirname($composerPath)));

            return false;
        }

        $fullPath = $realPath . '/' . basename($composerPath);
        $backupPath = $fullPath . '.backup';

        if (file_exists($fullPath)) {
            if (!copy($fullPath, $backupPath)) {
                $this->logger->error(sprintf('Failed to create backup: %s', $backupPath));

                return false;
            }
        }

        $jsonContent = json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($jsonContent === false) {
            $this->logger->error('Failed to encode composer data to JSON');

            return false;
        }

        $tempPath = $fullPath . '.tmp';

        if (file_put_contents($tempPath, $jsonContent, LOCK_EX) === false) {
            $this->logger->error(sprintf('Failed to write temporary file: %s', $tempPath));

            return false;
        }

        if (!rename($tempPath, $fullPath)) {
            $this->logger->error(sprintf('Failed to rename temporary file to: %s', $fullPath));
            unlink($tempPath);

            return false;
        }

        if (file_exists($backupPath)) {
            unlink($backupPath);
        }

        return true;
    }

    public function getPackageVersion(string $composerPath, string $packageName): ?string
    {
        $composerData = $this->readComposerJson($composerPath);

        if ($composerData === null) {
            return null;
        }

        if (
            isset($composerData['require'])
            && is_array($composerData['require'])
            && isset($composerData['require'][$packageName])
        ) {
            $version = $composerData['require'][$packageName];
            return is_string($version) ? $version : null;
        }

        if (
            isset($composerData['require-dev'])
            && is_array($composerData['require-dev'])
            && isset($composerData['require-dev'][$packageName])
        ) {
            $version = $composerData['require-dev'][$packageName];
            return is_string($version) ? $version : null;
        }

        return null;
    }

    public function updatePackageVersion(string $composerPath, string $packageName, string $version): bool
    {
        $composerData = $this->readComposerJson($composerPath);

        if ($composerData === null) {
            return false;
        }

        if (!isset($composerData['require'])) {
            $composerData['require'] = [];
        }

        if (!is_array($composerData['require'])) {
            $composerData['require'] = [];
        }

        $composerData['require'][$packageName] = $version;

        return $this->writeComposerJson($composerPath, $composerData);
    }

    public function createDependencyJson(string $moduleName, ?string $description = null): bool
    {
        if ($description === null || trim($description) === '') {
            throw new \InvalidArgumentException('Description cannot be null or empty');
        }

        $dependencyPath = getcwd() . '/Bundles/' . $moduleName . '/dependency.json';
        $realPath = realpath(dirname($dependencyPath));

        if ($realPath === false) {
            $this->logger->error(sprintf('Invalid module directory: %s', dirname($dependencyPath)));

            return false;
        }

        $fullPath = $realPath . '/dependency.json';

        // If file exists, read and check content
        if (file_exists($fullPath)) {
            $existingContent = file_get_contents($fullPath);

            if ($existingContent === false) {
                $this->logger->error(sprintf('Failed to read existing dependency file: %s', $fullPath));
                return false;
            }

            $existingData = json_decode($existingContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error(sprintf('Invalid JSON in existing dependency file: %s', json_last_error_msg()));
                return false;
            }

            if (!is_array($existingData)) {
                $this->logger->error('Existing dependency file does not contain a valid JSON object');
                return false;
            }

            // Check if spryker/transfer already exists in include section
            if (isset($existingData['include']) && is_array($existingData['include'])) {
                if (isset($existingData['include']['spryker/transfer'])) {
                    // Package already exists, no need to add it
                    return true;
                }

                // Add spryker/transfer to existing include array
                $existingData['include']['spryker/transfer'] = $description;
            } else {
                // Create include section with spryker/transfer
                $existingData['include'] = [
                    'spryker/transfer' => $description,
                ];
            }

            $dependencyData = $existingData;
        } else {
            // Create new dependency data
            $dependencyData = [
                'include' => [
                    'spryker/transfer' => $description,
                ],
            ];
        }

        $jsonContent = json_encode($dependencyData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($jsonContent === false) {
            $this->logger->error('Failed to encode dependency data to JSON');

            return false;
        }

        if (file_put_contents($fullPath, $jsonContent, LOCK_EX) === false) {
            $this->logger->error(sprintf('Failed to write dependency file: %s', $fullPath));

            return false;
        }

        return true;
    }
}
