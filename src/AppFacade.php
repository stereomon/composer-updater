<?php

declare(strict_types=1);

namespace App;

use App\DependencyUpdater\DependencyUpdaterService;
use App\DependencyUpdater\GitHubPRAnalyzerService;
use App\Greeting\GreetingService;
use App\Shared\Transfer\AnalyzePRRequestTransfer;
use App\Shared\Transfer\AnalyzePRResponseTransfer;
use App\Shared\Transfer\GreetingRequestTransfer;
use App\Shared\Transfer\GreetingResponseTransfer;
use App\Shared\Transfer\UpdateDependencyRequestTransfer;
use App\Shared\Transfer\UpdateDependencyResponseTransfer;

/**
 * Main application facade providing access to all vertical slice functionality.
 * This is the only facade in the application - do not create separate facades for vertical slices.
 */
class AppFacade
{
    public function __construct(
        private readonly GreetingService $greetingService,
        private readonly GitHubPRAnalyzerService $gitHubPRAnalyzerService,
        private readonly DependencyUpdaterService $dependencyUpdaterService
    ) {
    }

    public function greetUser(GreetingRequestTransfer $greetingRequestTransfer): GreetingResponseTransfer
    {
        return $this->greetingService->greetUser($greetingRequestTransfer);
    }

    public function analyzePR(AnalyzePRRequestTransfer $analyzePRRequestTransfer): AnalyzePRResponseTransfer
    {
        return $this->gitHubPRAnalyzerService->analyzePR($analyzePRRequestTransfer);
    }

    public function updateDependency(
        UpdateDependencyRequestTransfer $updateDependencyRequestTransfer
    ): UpdateDependencyResponseTransfer {
        return $this->dependencyUpdaterService->updateDependency($updateDependencyRequestTransfer);
    }
}
