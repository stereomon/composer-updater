<?php

declare(strict_types=1);

namespace App\Greeting;

use App\Shared\Transfer\ErrorTransfer;
use App\Shared\Transfer\GreetingRequestTransfer;
use App\Shared\Transfer\GreetingResponseTransfer;
use Psr\Log\LoggerInterface;

class GreetingService
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function greetUser(GreetingRequestTransfer $greetingRequestTransfer): GreetingResponseTransfer
    {
        $name = $greetingRequestTransfer->getName();

        if (empty(trim($name))) {
            $this->logger->warning('Empty name provided for greeting');

            return new GreetingResponseTransfer(
                false,
                '',
                [new ErrorTransfer('Name cannot be empty')]
            );
        }

        $message = sprintf('Hello %s!', $name);

        return new GreetingResponseTransfer(
            true,
            $message
        );
    }
}
