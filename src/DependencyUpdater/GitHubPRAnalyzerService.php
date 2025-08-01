<?php

declare(strict_types=1);

namespace App\DependencyUpdater;

use App\Shared\Transfer\AnalyzePRRequestTransfer;
use App\Shared\Transfer\AnalyzePRResponseTransfer;
use App\Shared\Transfer\ErrorTransfer;
use Github\Api\PullRequest;
use Github\AuthMethod;
use Github\Client;
use Psr\Log\LoggerInterface;

class GitHubPRAnalyzerService
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function analyzePR(AnalyzePRRequestTransfer $analyzePRRequestTransfer): AnalyzePRResponseTransfer
    {
        $repositoryOwner = $analyzePRRequestTransfer->getRepositoryOwner();

        if (empty(trim($repositoryOwner))) {
            $this->logger->warning('Empty repository owner provided');

            return new AnalyzePRResponseTransfer(
                false,
                [],
                '',
                [new ErrorTransfer('Repository owner cannot be empty')]
            );
        }

        $repositoryName = $analyzePRRequestTransfer->getRepositoryName();

        if (empty(trim($repositoryName))) {
            $this->logger->warning('Empty repository name provided');

            return new AnalyzePRResponseTransfer(
                false,
                [],
                '',
                [new ErrorTransfer('Repository name cannot be empty')]
            );
        }

        $pullRequestNumber = $analyzePRRequestTransfer->getPullRequestNumber();

        if ($pullRequestNumber <= 0) {
            $this->logger->warning('Invalid pull request number provided');

            return new AnalyzePRResponseTransfer(
                false,
                [],
                '',
                [new ErrorTransfer('Pull request number must be greater than zero')]
            );
        }

        $githubToken = $analyzePRRequestTransfer->getGithubToken();

        if ($githubToken === null || empty(trim($githubToken))) {
            $this->logger->warning('No GitHub token provided');

            return new AnalyzePRResponseTransfer(
                false,
                [],
                '',
                [new ErrorTransfer('GitHub token is required for API access')]
            );
        }

        try {
            $client = new Client();
            $client->authenticate($githubToken, null, AuthMethod::ACCESS_TOKEN);

            /** @var PullRequest $pullRequestApi */
            $pullRequestApi = $client->api('pull_request');
            $pullRequestApi->configure('diff');
            $diffContent = $pullRequestApi->show($repositoryOwner, $repositoryName, $pullRequestNumber);

            if (!is_string($diffContent)) {
                $this->logger->error('Expected diff content as string, got different type');

                return new AnalyzePRResponseTransfer(
                    false,
                    [],
                    '',
                    [new ErrorTransfer('Invalid diff content format received from GitHub API')]
                );
            }

            $changedModules = $this->parseModulesFromDiff($diffContent);
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('GitHub API error: %s', $exception->getMessage()));

            return new AnalyzePRResponseTransfer(
                false,
                [],
                '',
                [new ErrorTransfer('Failed to fetch PR diff from GitHub')]
            );
        }

        $message = sprintf('Found %d modules with transfer.xml changes', count($changedModules));

        return new AnalyzePRResponseTransfer(true, $changedModules, $message);
    }

    /**
     * @return string[]
     */
    private function parseModulesFromDiff(string $diff): array
    {
        $changedModules = [];
        $lines = explode("\n", $diff);

        foreach ($lines as $line) {
            if (!str_starts_with($line, '+++') && !str_starts_with($line, '---')) {
                continue;
            }

            if (!str_contains($line, 'transfer.xml')) {
                continue;
            }

            preg_match('/Bundles\/([^\/]+)\//', $line, $matches);

            if (!isset($matches[1])) {
                continue;
            }

            $moduleName = $matches[1];

            if (in_array($moduleName, $changedModules, true)) {
                continue;
            }

            $changedModules[] = $moduleName;
        }

        return $changedModules;
    }
}
