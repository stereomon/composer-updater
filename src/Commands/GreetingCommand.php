<?php

declare(strict_types=1);

namespace App\Commands;

use App\Shared\Transfer\GreetingRequestTransfer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:greet',
    description: 'Greets a user with their name',
)]
class GreetingCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the person to greet');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @phpstan-var string */
        $nameArgument = $input->getArgument('name');

        $greetingRequestTransfer = new GreetingRequestTransfer($nameArgument);
        $greetingResponseTransfer = $this->appFacade->greetUser($greetingRequestTransfer);

        if (!$greetingResponseTransfer->isSuccessful()) {
            $this->displayErrors($greetingResponseTransfer->getErrors(), $io);

            return static::FAILURE;
        }

        $greetingMessage = $greetingResponseTransfer->getGreetingMessage();

        if ($greetingMessage === null) {
            $io->error('No greeting message was generated');

            return self::FAILURE;
        }

        $io->success($greetingMessage);

        return self::SUCCESS;
    }
}
