<?php

declare(strict_types=1);

namespace App\Commands;

use App\AppFacade;
use App\Shared\Transfer\ErrorTransfer;
use App\Shared\Transfer\ResponseTransfer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractCommand extends Command
{
    public function __construct(
        protected readonly AppFacade $appFacade
    ) {
        parent::__construct();
    }

    /**
     * @param ErrorTransfer[] $errors
     */
    protected function displayErrors(array $errors, SymfonyStyle $io): void
    {
        $io->error('The following errors occurred:');

        foreach ($errors as $error) {
            $io->writeln('• ' . $error->getMessage());
        }
    }
}
