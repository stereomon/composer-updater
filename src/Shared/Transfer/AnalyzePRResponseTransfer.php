<?php

declare(strict_types=1);

namespace App\Shared\Transfer;

class AnalyzePRResponseTransfer extends ResponseTransfer
{
    /**
     * @param string[] $changedModules
     * @param ErrorTransfer[] $errors
     */
    public function __construct(
        bool $isSuccessful = false,
        private array $changedModules = [],
        private ?string $analysisMessage = null,
        array $errors = []
    ) {
        parent::__construct($isSuccessful, $errors);
    }

    /**
     * @return string[]
     */
    public function getChangedModules(): array
    {
        return $this->changedModules;
    }

    /**
     * @param string[] $changedModules
     */
    public function setChangedModules(array $changedModules): static
    {
        $this->changedModules = $changedModules;

        return $this;
    }

    public function getAnalysisMessage(): ?string
    {
        return $this->analysisMessage;
    }

    public function setAnalysisMessage(?string $analysisMessage): static
    {
        $this->analysisMessage = $analysisMessage;

        return $this;
    }
}
