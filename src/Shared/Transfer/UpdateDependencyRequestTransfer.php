<?php

declare(strict_types=1);

namespace App\Shared\Transfer;

class UpdateDependencyRequestTransfer
{
    /**
     * @param string[] $moduleNames
     */
    public function __construct(
        private readonly array $moduleNames,
        private readonly string $packageName,
        private readonly string $expectedVersion,
        private readonly ?string $description = null
    ) {
    }

    /**
     * @return string[]
     */
    public function getModuleNames(): array
    {
        return $this->moduleNames;
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    public function getExpectedVersion(): string
    {
        return $this->expectedVersion;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
