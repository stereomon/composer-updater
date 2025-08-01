# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Development
- `composer install` - Install dependencies
- `./bin/console app:greet <name>` - Run the greeting command example

### Quality Assurance
- `composer run phpstan` - Run PHPStan static analysis
- `composer run cs-check` - Check code style issues
- `composer run cs-fix` - Fix code style issues automatically
- `composer run test` - Run Codeception tests
- `composer run test-coverage` - Run tests with coverage
- `composer run test-coverage-html` - Generate HTML coverage report
- `composer run local-ci` - Run all QA tools (fix, check, analyze, test)

### Testing
- `codecept run` - Run all tests
- `codecept run unit` - Run unit tests only
- `codecept run integration` - Run integration tests only

## Architecture

This is a Symfony Console application template using vertical slice architecture with dependency injection.

### Core Components

- **AppFacade** (`src/AppFacade.php`) - Single application facade providing access to all vertical slice functionality. Never create separate facades for vertical slices.
- **Kernel** (`src/Kernel.php`) - Symfony MicroKernel with Runtime component, loads different service configurations for dev/test environments
- **AbstractCommand** (`src/Commands/AbstractCommand.php`) - Base class for all console commands with error handling and AppFacade dependency

### Directory Structure

```
src/
├── Commands/           # All console commands
├── [VerticalSlice]/    # Domain-specific functionality (e.g., Greeting/)
│   └── [Service].php   # Business logic services
├── Shared/Transfer/    # Data Transfer Objects
├── AppFacade.php       # Single application facade
└── Kernel.php          # Application kernel
```

### Key Patterns

- **Vertical Slices**: Organize code by feature/domain rather than technical layers
- **Transfer Objects**: Use DTOs suffixed with "Transfer" (not "DTO") for all method inputs/outputs
- **Response Pattern**: Output transfers must have `isSuccessful()` method and `getErrors()` for error handling
- **Dependency Injection**: All services autowired through `config/services.php`, test services in `config/services_test.php`
- **Command Pattern**: All commands extend AbstractCommand, receive AppFacade dependency, use SymfonyStyle for output

### Transfer Objects

All data transfer objects follow these conventions:
- Suffixed with "Transfer" (e.g., `GreetingRequestTransfer`, `GreetingResponseTransfer`)
- Response transfers must implement `isSuccessful(): bool` method
- Failed responses contain array of `ErrorTransfer` objects accessible via `getErrors()`
- Variable names match object names: `$greetingRequestTransfer` for `GreetingRequestTransfer`

### Testing

- Uses Codeception framework with Unit and Integration test suites
- Test method names use Given-When-Then syntax
- Each class must be fully tested with happy and unhappy cases
- Prefer Integration tests over Unit tests where possible
- Tests use Arrange-Act-Assert pattern
- Console Helper module provides access to Symfony Console Tester
- Symfony Helper module provides container access for dependency injection in tests

### Code Standards

- PHP 8.3+ with strict types (`declare(strict_types=1)`)
- PSR-12 coding standard with additional rules (see `phpcs.xml`)
- PHPStan at maximum level
- Use typed property promotion, type hints everywhere
- Follow Clean Code principles with early returns instead of if/else constructs
- Use `sprintf()` for string concatenation
- Monolog integration for logging (errors only, not success messages)
- Add a new line before and after any PHP function call like if, foreach
- Do not use if/else constructs
- Always have a max 2 depth indentation level of methods
- Avoid double-quotes where possible