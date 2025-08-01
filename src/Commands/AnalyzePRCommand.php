<?php

declare(strict_types=1);

namespace App\Commands;

use App\Shared\Transfer\AnalyzePRRequestTransfer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:analyze-pr',
    description: 'Analyzes a GitHub PR diff to identify modules with transfer.xml changes',
)]
class AnalyzePRCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('owner', InputArgument::REQUIRED, 'The GitHub repository owner')
            ->addArgument('repo', InputArgument::REQUIRED, 'The GitHub repository name')
            ->addArgument('pr-number', InputArgument::REQUIRED, 'The pull request number')
            ->addOption('token', 't', InputOption::VALUE_OPTIONAL, 'GitHub personal access token');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @phpstan-var string */
        $ownerArgument = $input->getArgument('owner');

        /** @phpstan-var string */
        $repoArgument = $input->getArgument('repo');

        /** @phpstan-var string */
        $prNumberArgument = $input->getArgument('pr-number');

        /** @phpstan-var ?string */
        $tokenOption = $input->getOption('token');

        $prNumber = (int) $prNumberArgument;

        if ($prNumber <= 0) {
            $io->error('Pull request number must be a positive integer');

            return static::FAILURE;
        }

        $githubToken = $tokenOption ?? $_ENV['GITHUB_TOKEN'] ?? null;

        $analyzePRRequestTransfer = new AnalyzePRRequestTransfer(
            $ownerArgument,
            $repoArgument,
            $prNumber,
            $githubToken
        );

        $analyzePRResponseTransfer = $this->appFacade->analyzePR($analyzePRRequestTransfer);

        if (!$analyzePRResponseTransfer->isSuccessful()) {
            $this->displayErrors($analyzePRResponseTransfer->getErrors(), $io);

            return static::FAILURE;
        }

        $analysisMessage = $analyzePRResponseTransfer->getAnalysisMessage();

        if ($analysisMessage === null) {
            $io->error('No analysis message was generated');

            return self::FAILURE;
        }

        $changedModules = $analyzePRResponseTransfer->getChangedModules();

        $io->success($analysisMessage);

        if (!empty($changedModules)) {
            $io->section('Changed Modules:');
            $io->listing($changedModules);
        }

        return self::SUCCESS;
    }
}
