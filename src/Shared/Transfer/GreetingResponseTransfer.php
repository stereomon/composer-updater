<?php

declare(strict_types=1);

namespace App\Shared\Transfer;

class GreetingResponseTransfer extends ResponseTransfer
{
    public function __construct(
        bool $isSuccessful = false,
        private ?string $greetingMessage = null,
        array $errors = []
    ) {
        parent::__construct($isSuccessful, $errors);
    }

    public function getGreetingMessage(): ?string
    {
        return $this->greetingMessage;
    }

    public function setGreetingMessage(?string $greetingMessage): static
    {
        $this->greetingMessage = $greetingMessage;

        return $this;
    }
}
