name: "Composer Dependency Updater - Context-Rich PRP for AI Implementation"
description: |

## Purpose

Complete implementation guide for a composer dependency updater system that analyzes GitHub PRs and updates module dependencies. Optimized for AI agents to achieve working code through iterative refinement with comprehensive context and validation loops.

## Core Principles

1. **Context is King**: Include ALL necessary documentation, examples, and caveats
2. **Validation Loops**: Provide executable tests/lints the AI can run and fix
3. **Information Dense**: Use keywords and patterns from the codebase
4. **Progressive Success**: Start simple, validate, then enhance

---

## Goal

Build a complete composer dependency updater system with two console commands:
1. **app:analyze-pr** - Takes GitHub PR number, fetches diff, identifies changed modules with transfer.xml files
2. **app:update-dependency** - Takes module list, package name, version, updates composer.json files in module directories

End state: Global composer package that can analyze GitHub PRs and update dependencies across multiple modules in a project using getcwd() context.

## Why

- **Business value**: Automate dependency updates across multiple modules when PRs change transfer.xml files
- **Integration**: Works as global composer package, integrates with existing Symfony Console architecture
- **Problems solved**: Manual dependency tracking and updating across modules when transfer files change

## What

Two console commands with full GitHub API integration, file system operations, and composer.json manipulation.

### Success Criteria

- [ ] app:analyze-pr command fetches GitHub PR diff and identifies modules with transfer.xml changes
- [ ] app:update-dependency command updates/adds dependencies in module composer.json files
- [ ] Handles dependency.json files for spryker/transfer special case
- [ ] Works correctly with getcwd() for global installation context
- [ ] Comprehensive error handling and logging
- [ ] Full test coverage following existing patterns
- [ ] Passes all quality gates (cs-fix, phpstan, tests)

## All Needed Context

### Documentation & References

```yaml
# MUST READ - Include these in your context window
- url: https://docs.github.com/en/rest/pulls/pulls
  why: GitHub REST API for pull requests - fetching PR information and diffs

- url: https://docs.github.com/en/rest/authentication/authenticating-to-the-rest-api
  why: Authentication patterns for GitHub API access with personal tokens

- url: https://github.com/KnpLabs/php-github-api
  why: Recommended PHP GitHub API client library with diff/patch support

- url: https://getcomposer.org/doc/04-schema.md
  why: Composer.json schema documentation for proper dependency manipulation

- url: https://www.php.net/manual/en/ref.filesystem.php
  why: PHP file system functions for safe JSON file operations

- file: src/Commands/GreetingCommand.php
  why: CRITICAL - Follow this exact pattern for command structure, validation, error handling

- file: src/Commands/AbstractCommand.php
  why: CRITICAL - Base class pattern with AppFacade injection and displayErrors method

- file: src/AppFacade.php
  why: CRITICAL - Single facade pattern, never create separate facades per vertical slice

- file: src/Greeting/GreetingService.php
  why: CRITICAL - Service implementation pattern with early returns, logging, transfer objects

- file: src/Shared/Transfer/GreetingRequestTransfer.php
  why: CRITICAL - Request transfer pattern with readonly properties

- file: src/Shared/Transfer/GreetingResponseTransfer.php
  why: CRITICAL - Response transfer pattern extending ResponseTransfer

- file: tests/integration/Commands/GreetingCommandTest.php
  why: CRITICAL - Testing pattern with Given-When-Then method names, comprehensive edge cases

- file: config/services.php
  why: Service registration pattern for dependency injection
```

### Current Codebase Tree

```bash
src/
├── Commands/           # Console commands extending AbstractCommand
│   ├── AbstractCommand.php     # Base class with AppFacade injection
│   └── GreetingCommand.php     # Example command implementation
├── Greeting/           # Example vertical slice
│   └── GreetingService.php     # Service with early returns, logging
├── Shared/Transfer/    # All transfer objects
│   ├── ErrorTransfer.php
│   ├── ResponseTransfer.php
│   ├── GreetingRequestTransfer.php
│   └── GreetingResponseTransfer.php
├── AppFacade.php       # Single application facade
└── Kernel.php          # Symfony MicroKernel
```

### Desired Codebase Tree with New Files

```bash
src/
├── Commands/
│   ├── AnalyzePRCommand.php           # GitHub PR analysis command
│   └── UpdateDependencyCommand.php    # Dependency update command
├── DependencyUpdater/                 # New vertical slice
│   ├── GitHubPRAnalyzerService.php    # PR diff analysis service  
│   ├── DependencyUpdaterService.php   # Composer.json manipulation service
│   └── ComposerFileHandler.php        # File operations service
├── Shared/Transfer/
│   ├── AnalyzePRRequestTransfer.php   # PR analysis request
│   ├── AnalyzePRResponseTransfer.php  # PR analysis response  
│   ├── UpdateDependencyRequestTransfer.php   # Dependency update request
│   └── UpdateDependencyResponseTransfer.php  # Dependency update response
└── AppFacade.php                      # Updated with new methods
```

### Known Gotchas & Library Quirks

```php
// CRITICAL: This codebase follows specific patterns
// - All commands extend AbstractCommand with AppFacade injection
// - All services use early returns (no if/else constructs)
// - Transfer objects suffixed with "Transfer" not "DTO"
// - Response transfers must implement isSuccessful() and getErrors()
// - Variable names match transfer class names: $greetingRequestTransfer
// - Use sprintf() for string concatenation, not concatenation operators
// - Maximum 2-level indentation depth in methods
// - Logging for errors only, not success messages
// - PHP 8.3+ with strict types declaration

// CRITICAL: Composer.json manipulation
// - Use Composer\Json\JsonManipulator for preserving formatting
// - Always validate JSON with json_last_error() after parsing
// - Use atomic file operations with backup/restore on failure
// - Handle both require and require-dev sections correctly

// CRITICAL: GitHub API
// - Use knplabs/github-api library with proper authentication
// - Rate limiting: 5000 requests/hour authenticated, 60 unauthenticated
// - Use media type application/vnd.github.diff for diff format
// - Personal access tokens required, store in environment variables

// CRITICAL: File operations
// - getcwd() context crucial for global composer package operation
// - Use realpath() to prevent directory traversal attacks
// - Atomic operations with file locking for concurrent access
// - Path pattern: getcwd() . '/Bundles/' . $moduleName . '/composer.json'
```

## Implementation Blueprint

### Data Models and Structure

Create transfer objects following existing patterns with typed property promotion and response inheritance.

```php
// Request transfers with readonly properties
class AnalyzePRRequestTransfer
{
    public function __construct(
        private readonly string $repositoryOwner,
        private readonly string $repositoryName,
        private readonly int $pullRequestNumber,
        private readonly ?string $githubToken = null
    ) {}
}

// Response transfers extending ResponseTransfer
class UpdateDependencyResponseTransfer extends ResponseTransfer
{
    public function __construct(
        bool $isSuccessful = false,
        private array $updatedModules = [],
        private ?string $updateMessage = null,
        array $errors = []
    ) {
        parent::__construct($isSuccessful, $errors);
    }
}
```

### List of Tasks to Complete in Order

```yaml
Task 1:
CREATE src/Shared/Transfer/AnalyzePRRequestTransfer.php:
  - MIRROR pattern from: src/Shared/Transfer/GreetingRequestTransfer.php
  - PROPERTIES: repositoryOwner, repositoryName, pullRequestNumber, githubToken
  - KEEP readonly property pattern identical

Task 2:
CREATE src/Shared/Transfer/AnalyzePRResponseTransfer.php:
  - MIRROR pattern from: src/Shared/Transfer/GreetingResponseTransfer.php
  - EXTEND ResponseTransfer with isSuccessful/getErrors
  - PROPERTIES: changedModules array, analysisMessage string

Task 3:
CREATE src/Shared/Transfer/UpdateDependencyRequestTransfer.php:
  - FOLLOW same readonly property pattern
  - PROPERTIES: moduleNames array, packageName, expectedVersion, description

Task 4:
CREATE src/Shared/Transfer/UpdateDependencyResponseTransfer.php:
  - EXTEND ResponseTransfer base class
  - PROPERTIES: updatedModules array, updateMessage string
  - INCLUDE fluent setters returning static

Task 5:
CREATE src/DependencyUpdater/GitHubPRAnalyzerService.php:
  - MIRROR pattern from: src/Greeting/GreetingService.php
  - INJECT LoggerInterface in constructor
  - METHOD: analyzePR(AnalyzePRRequestTransfer): AnalyzePRResponseTransfer
  - USE early returns for validation, no if/else constructs

Task 6:
CREATE src/DependencyUpdater/ComposerFileHandler.php:
  - INJECT LoggerInterface in constructor  
  - METHODS: readComposerJson, writeComposerJson, createDependencyJson
  - USE atomic file operations with backup/restore
  - HANDLE getcwd() context for path building

Task 7:
CREATE src/DependencyUpdater/DependencyUpdaterService.php:
  - MIRROR service pattern with early returns
  - INJECT LoggerInterface and ComposerFileHandler
  - METHOD: updateDependency(UpdateDependencyRequestTransfer): UpdateDependencyResponseTransfer
  - USE sprintf() for string formatting

Task 8:
MODIFY src/AppFacade.php:
  - FIND constructor injection pattern
  - ADD DependencyUpdaterService and GitHubPRAnalyzerService to constructor
  - ADD METHOD: analyzePR(AnalyzePRRequestTransfer): AnalyzePRResponseTransfer
  - ADD METHOD: updateDependency(UpdateDependencyRequestTransfer): UpdateDependencyResponseTransfer
  - PRESERVE existing facade method pattern

Task 9:
CREATE src/Commands/AnalyzePRCommand.php:
  - EXTEND AbstractCommand following GreetingCommand pattern exactly
  - USE #[AsCommand] attribute with app:analyze-pr name
  - CONFIGURE arguments: owner, repo, pr-number
  - FOLLOW execute method pattern with SymfonyStyle
  - USE displayErrors method from base class

Task 10:
CREATE src/Commands/UpdateDependencyCommand.php:
  - EXTEND AbstractCommand with same pattern
  - USE #[AsCommand] attribute with app:update-dependency name  
  - CONFIGURE arguments: modules, package-name, version, description
  - FOLLOW exact error handling pattern from GreetingCommand

Task 11:
CREATE tests/integration/Commands/AnalyzePRCommandTest.php:
  - MIRROR pattern from: tests/integration/Commands/GreetingCommandTest.php
  - USE Given-When-Then method naming convention
  - TEST both success and error scenarios with CommandTester
  - MOCK GitHub API service responses

Task 12:
CREATE tests/integration/Commands/UpdateDependencyCommandTest.php:
  - FOLLOW same testing pattern
  - TEST file system operations with temporary test files
  - VERIFY composer.json updates and dependency.json creation
  - INCLUDE edge cases: missing files, permission errors

Task 13:
CREATE tests/unit/DependencyUpdater/GitHubPRAnalyzerServiceTest.php:
  - TEST service logic with mocked HTTP client
  - VERIFY diff parsing and module identification
  - TEST error handling for API failures and rate limiting

Task 14:
CREATE tests/unit/DependencyUpdater/DependencyUpdaterServiceTest.php:
  - TEST dependency update logic
  - MOCK ComposerFileHandler for isolation
  - VERIFY business logic without file system dependencies
```

### Pseudocode with Critical Details

```php
// Task 5: GitHubPRAnalyzerService
public function analyzePR(AnalyzePRRequestTransfer $analyzePRRequestTransfer): AnalyzePRResponseTransfer
{
    // PATTERN: Always validate input first
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

    // CRITICAL: GitHub API rate limiting - use knplabs/github-api
    $client = new Github\Client();
    $client->authenticate($githubToken, null, Github\AuthMethod::ACCESS_TOKEN);
    
    try {
        // GOTCHA: Use diff media type for raw diff content
        $pullRequestApi = $client->api('pull_request');
        $pullRequestApi->configure('diff');
        $diff = $pullRequestApi->show($repositoryOwner, $repositoryName, $prNumber);
        
        // PATTERN: Parse diff to find changed modules with transfer.xml
        $changedModules = $this->parseModulesFromDiff($diff);
        
    } catch (Exception $e) {
        $this->logger->error(sprintf('GitHub API error: %s', $e->getMessage()));

        return new AnalyzePRResponseTransfer(
            false,
            [],
            '',
            [new ErrorTransfer('Failed to fetch PR diff from GitHub')]
        );
    }

    // PATTERN: Success response with detailed message
    $message = sprintf('Found %d modules with transfer.xml changes', count($changedModules));
    
    return new AnalyzePRResponseTransfer(true, $changedModules, $message);
}

// Task 7: DependencyUpdaterService  
public function updateDependency(UpdateDependencyRequestTransfer $updateDependencyRequestTransfer): UpdateDependencyResponseTransfer
{
    $packageName = $updateDependencyRequestTransfer->getPackageName();
    $expectedVersion = $updateDependencyRequestTransfer->getExpectedVersion();
    
    // PATTERN: Early return validation
    if (empty(trim($packageName))) {
        return new UpdateDependencyResponseTransfer(
            false,
            [],
            '',
            [new ErrorTransfer('Package name cannot be empty')]
        );
    }

    $updatedModules = [];
    
    foreach ($updateDependencyRequestTransfer->getModuleNames() as $moduleName) {
        // CRITICAL: Use getcwd() context for global composer package
        $composerPath = getcwd() . '/Bundles/' . $moduleName . '/composer.json';
        
        if (!$this->composerFileHandler->composerFileExists($composerPath)) {
            $this->logger->warning(sprintf('Composer file not found: %s', $composerPath));
            continue;
        }
        
        // PATTERN: Read current version, compare with expected
        $currentVersion = $this->composerFileHandler->getPackageVersion($composerPath, $packageName);
        
        if ($currentVersion === $expectedVersion) {
            continue; // Already correct version
        }
        
        // CRITICAL: Handle spryker/transfer special case with dependency.json
        if ($packageName === 'spryker/transfer' && $currentVersion === null) {
            $this->composerFileHandler->createDependencyJson($moduleName);
        }
        
        // PATTERN: Update composer.json atomically
        $this->composerFileHandler->updatePackageVersion($composerPath, $packageName, $expectedVersion);
        $updatedModules[] = $moduleName;
    }

    // PATTERN: Standardized response format
    $message = sprintf('Updated %d modules with %s version %s', 
        count($updatedModules), $packageName, $expectedVersion);
    
    return new UpdateDependencyResponseTransfer(true, $updatedModules, $message);
}
```

### Integration Points

```yaml
DEPENDENCIES:
  - add to: composer.json root
  - packages: 
    - "knplabs/github-api": "^3.0"
    - "composer/composer": "^2.0" # For JsonManipulator

CONFIG:
  - add to: config/services.php
  - pattern: "Auto-wire new services in src/DependencyUpdater/"
  - environment: "GITHUB_TOKEN for API authentication"

COMMANDS:
  - register: Automatic via #[AsCommand] attribute
  - pattern: "app:analyze-pr and app:update-dependency"
  - global: "Works with getcwd() for global composer installation"
```

## Validation Loop

### Level 1: Syntax & Style

```bash
# Run these FIRST - fix any errors before proceeding
composer cs-fix                     # Auto-fix coding standards
composer phpstan                    # Static analysis - MUST pass at maximum level

# Expected: No errors. If errors, READ the error message and fix systematically.
```

### Level 2: Unit Tests

```php
// CREATE comprehensive test coverage following existing patterns:

// AnalyzePRCommandTest.php
public function testGivenValidPRNumberWhenAnalyzingPRThenModulesAreIdentified(): void
{
    // Arrange - Mock GitHub API response with diff containing transfer.xml changes
    $mockGitHubService = $this->createMock(GitHubPRAnalyzerService::class);
    $mockResponse = new AnalyzePRResponseTransfer(true, ['ModuleA', 'ModuleB'], 'Found 2 modules');
    $mockGitHubService->method('analyzePR')->willReturn($mockResponse);
    
    $this->tester->set(GitHubPRAnalyzerService::class, $mockGitHubService);
    $commandTester = $this->tester->getCommandTester(AnalyzePRCommand::class);

    // Act
    $commandTester->execute(['owner' => 'test', 'repo' => 'repo', 'pr-number' => '123']);

    // Assert
    $this->assertStringContainsString('Found 2 modules', $commandTester->getDisplay());
    $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
}

public function testGivenInvalidPRNumberWhenAnalyzingPRThenErrorIsDisplayed(): void
{
    // Test error handling with proper ErrorTransfer usage
    $mockGitHubService = $this->createMock(GitHubPRAnalyzerService::class);
    $mockResponse = new AnalyzePRResponseTransfer(
        false, [], '', [new ErrorTransfer('PR not found')]
    );
    $mockGitHubService->method('analyzePR')->willReturn($mockResponse);
    
    $this->tester->set(GitHubPRAnalyzerService::class, $mockGitHubService);
    $commandTester = $this->tester->getCommandTester(AnalyzePRCommand::class);

    // Act
    $commandTester->execute(['owner' => 'test', 'repo' => 'repo', 'pr-number' => '999']);

    // Assert
    $this->assertStringContainsString('PR not found', $commandTester->getDisplay());
    $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
}

// DependencyUpdaterServiceTest.php - Test business logic
public function testGivenExistingPackageWhenUpdatingToSameVersionThenNoChangesMade(): void
{
    // Test that identical versions are skipped
}

public function testGivenSprykerTransferPackageWhenNotInComposerThenDependencyJsonCreated(): void
{
    // Test special case for spryker/transfer dependency.json creation
}
```

```bash
# Run and iterate until passing:
composer test
# If failing: Read error output, understand root cause, fix code, re-run
# NEVER mock successful tests - ensure real functionality works
```

### Level 3: Integration Testing

```bash
# Test commands with real GitHub API (use test repository)
export GITHUB_TOKEN="your_test_token"
php bin/console app:analyze-pr octocat Hello-World 1

# Expected: Command executes successfully, identifies modules
# If error: Check logs, verify token permissions, API rate limits

# Test dependency updates with test files
mkdir -p test_modules/TestModule
echo '{"require": {"old/package": "1.0"}}' > test_modules/TestModule/composer.json
php bin/console app:update-dependency TestModule symfony/console ^6.0

# Expected: composer.json updated with new dependency
# Verify: Check file contents, backup/restore functionality
```

### Level 4: End-to-End Validation

```bash
# Complete workflow test
# 1. Analyze a real PR with transfer.xml changes
php bin/console app:analyze-pr spryker spryker-core 12345

# 2. Update dependencies based on analysis results  
php bin/console app:update-dependency ModuleA,ModuleB spryker/transfer ^1.2.0 "Transfer update"

# 3. Verify all composer.json files updated correctly
# 4. Check dependency.json files created for spryker/transfer
# 5. Validate no corruption of existing composer.json formatting

# Performance testing
# - Test with large PRs (100+ file changes)
# - Test with multiple module updates (50+ modules)
# - Verify rate limiting handling with GitHub API
```

## Final Validation Checklist

- [ ] All tests pass: `composer test` 
- [ ] No linting errors: `composer cs-check`
- [ ] No type errors: `composer phpstan`
- [ ] Both commands work with real GitHub PRs
- [ ] File operations are atomic and safe
- [ ] Error cases handled gracefully with proper logging
- [ ] GitHub API rate limiting respected
- [ ] Global composer package context (getcwd()) works correctly
- [ ] Special spryker/transfer dependency.json case implemented

---

## Anti-Patterns to Avoid

- ❌ Don't create separate facades for vertical slices (use single AppFacade)
- ❌ Don't use if/else constructs - use early returns pattern
- ❌ Don't catch generic Exception - be specific about error types
- ❌ Don't hardcode GitHub API URLs - use library abstractions
- ❌ Don't manipulate JSON strings directly - use JsonManipulator
- ❌ Don't ignore file operation failures - implement proper rollback
- ❌ Don't skip atomic operations for composer.json updates
- ❌ Don't use string concatenation - use sprintf() pattern
- ❌ Don't create transfer objects without proper inheritance
- ❌ Don't skip comprehensive error testing scenarios

---

## PRP Confidence Score: 9/10

This PRP provides comprehensive context, follows established codebase patterns exactly, includes detailed implementation steps with pseudocode, and provides multiple validation loops. The AI agent has everything needed for one-pass implementation success through iterative refinement.