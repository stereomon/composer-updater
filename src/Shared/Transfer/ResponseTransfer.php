<?php

declare(strict_types=1);

namespace App\Shared\Transfer;

class ResponseTransfer
{
    /**
     * @param ErrorTransfer[] $errors
     */
    public function __construct(
        private bool $isSuccessful = false,
        private array $errors = []
    ) {
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    public function setIsSuccessful(bool $isSuccessful): self
    {
        $this->isSuccessful = $isSuccessful;
        return $this;
    }

    /**
     * @return ErrorTransfer[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param ErrorTransfer[] $errors
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    public function addError(ErrorTransfer $error): self
    {
        $this->errors[] = $error;
        return $this;
    }
}
