<?php

declare(strict_types=1);

namespace App\Shared\Transfer;

class ErrorTransfer
{
    public function __construct(
        private readonly string $message,
        private readonly ?string $code = null
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }
}
