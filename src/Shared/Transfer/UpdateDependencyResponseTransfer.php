<?php

declare(strict_types=1);

namespace App\Shared\Transfer;

class UpdateDependencyResponseTransfer extends ResponseTransfer
{
    /**
     * @param string[] $updatedModules
     * @param ErrorTransfer[] $errors
     */
    public function __construct(
        bool $isSuccessful = false,
        private array $updatedModules = [],
        private ?string $updateMessage = null,
        array $errors = []
    ) {
        parent::__construct($isSuccessful, $errors);
    }

    /**
     * @return string[]
     */
    public function getUpdatedModules(): array
    {
        return $this->updatedModules;
    }

    /**
     * @param string[] $updatedModules
     */
    public function setUpdatedModules(array $updatedModules): static
    {
        $this->updatedModules = $updatedModules;

        return $this;
    }

    public function getUpdateMessage(): ?string
    {
        return $this->updateMessage;
    }

    public function setUpdateMessage(?string $updateMessage): static
    {
        $this->updateMessage = $updateMessage;

        return $this;
    }
}
