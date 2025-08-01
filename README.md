# Console Application Template

A Symfony Console application template using vertical slice architecture, designed for building "Pop Up Applications" with best practices built-in.

## Features

- Symfony Console with MicroKernel and Runtime component
- Vertical slice architecture with dependency injection
- Comprehensive testing setup (Codeception)
- Code quality tools (PHPStan, PHP_CodeSniffer)
- Integration with [PRP Framework](https://github.com/Wirasm/PRPs-agentic-eng)

## Quick Start

```bash
# Install dependencies
composer install

# Run example command
./bin/console app:greet YourName

# Run quality assurance
composer run local-ci
```

## Development

For detailed development guidelines, architecture patterns, and coding standards, see [CLAUDE.md](CLAUDE.md).

## Quality Assurance Commands

- `composer run phpstan` - Static analysis
- `composer run cs-check` - Check code style
- `composer run cs-fix` - Fix code style
- `composer run test` - Run tests
- `composer run local-ci` - Run all QA tools