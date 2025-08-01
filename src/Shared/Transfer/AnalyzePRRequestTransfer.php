<?php

declare(strict_types=1);

namespace App\Shared\Transfer;

class AnalyzePRRequestTransfer
{
    public function __construct(
        private readonly string $repositoryOwner,
        private readonly string $repositoryName,
        private readonly int $pullRequestNumber,
        private readonly ?string $githubToken = null
    ) {
    }

    public function getRepositoryOwner(): string
    {
        return $this->repositoryOwner;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function getPullRequestNumber(): int
    {
        return $this->pullRequestNumber;
    }

    public function getGithubToken(): ?string
    {
        return $this->githubToken;
    }
}
