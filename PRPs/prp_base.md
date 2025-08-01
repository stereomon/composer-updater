name: "Base PRP Template v2 - Context-Rich with Validation Loops"
description: |

## Purpose

Template optimized for AI agents to implement features with sufficient context and self-validation capabilities to achieve working code through iterative refinement.

## Core Principles

1. **Context is King**: Include ALL necessary documentation, examples, and caveats
2. **Validation Loops**: Provide executable tests/lints the AI can run and fix
3. **Information Dense**: Use keywords and patterns from the codebase
4. **Progressive Success**: Start simple, validate, then enhance

---

## Goal

[What needs to be built - be specific about the end state and desires]

## Why

- [Business value and user impact]
- [Integration with existing features]
- [Problems this solves and for whom]

## What

[User-visible behavior and technical requirements]

### Success Criteria

- [ ] [Specific measurable outcomes]

## All Needed Context

### Documentation & References (list all context needed to implement the feature)

```yaml
# MUST READ - Include these in your context window
- url: [Official API docs URL]
  why: [Specific sections/methods you'll need]

- file: [path/to/example.php]
  why: [Pattern to follow, gotchas to avoid]

- doc: [Library documentation URL]
  section: [Specific section about common pitfalls]
  critical: [Key insight that prevents common errors]

- docfile: [PRPs/ai_docs/file.md]
  why: [docs that the user has pasted in to the project]
```

### Current Codebase tree (run `tree --gitignore` in the root of the project) to get an overview of the codebase

```bash

```

### Desired Codebase tree with files to be added and responsibility of file

```bash

```

### Known Gotchas of our codebase & Library Quirks

```php
// CRITICAL: [Library name] requires [specific setup]
// Example: Symfony Console requires proper DI container setup
// Example: This ORM doesn't support batch inserts over 1000 records
// Example: We use Symfony 7.0 and PHP 8.3+
```

## Implementation Blueprint

### Data models and structure

Create the core data models, we ensure type safety and consistency.

```php
Examples:
 - Entity models with proper namespaces
 - Transfer objects with type declarations
```

### List of tasks to be completed to fulfill the PRP in the order they should be completed

```yaml
Task 1:
MODIFY src/ExistingModule.php:
  - FIND pattern: "class OldImplementation"
  - INJECT after line containing "protected function doSomething"
  - PRESERVE existing method signatures

CREATE src/NewFeature.php:
  - MIRROR pattern from: src/SimilarFeature.php
  - MODIFY class name and core logic
  - KEEP error handling pattern identical

...(...)

Task N:
...

```

### Per task pseudocode as needed added to each task

```php

// Task 1
// Pseudocode with CRITICAL details don't write entire code
public function newFeature(string $param): Result
{
    // PATTERN: Always validate input first (see src/Validators.php)
    $validated = $this->validateInput($param); // throws ValidationException

    // GOTCHA: This library requires proper DI container setup
    $connection = $this->connectionPool->getConnection(); // see config/services.php
    
    try {
        // PATTERN: Use existing retry mechanism
        $result = $this->retryService->execute(function() use ($validated) {
            // CRITICAL: API returns 429 if >10 req/sec
            $this->rateLimiter->acquire();

            return $this->externalApi->call($validated);
        }, 3);
    } finally {
        $this->connectionPool->releaseConnection($connection);
    }

    // PATTERN: Standardized response format
    return $this->responseFormatter->format($result); // see src/Utils/ResponseFormatter.php
}
```

### Integration Points

```yaml
DATABASE:
  - migration: "Add column 'feature_enabled' to users table"
  - index: "CREATE INDEX idx_feature_lookup ON users(feature_id)"

CONFIG:
  - add to: config/services.php
  - pattern: "parameters: feature_timeout: '%env(int:FEATURE_TIMEOUT:30)%'"

COMMANDS:
  - add to: src/Commands/
  - pattern: "extends AbstractCommand and register in services.php"
```

## Validation Loop

### Level 1: Syntax & Style

```bash
# Run these FIRST - fix any errors before proceeding
composer cs-fix                     # Auto-fix coding standards
composer phpstan                    # Static analysis

# Expected: No errors. If errors, READ the error and fix.
```

### Level 2: Unit Tests each new feature/file/function use existing test patterns

```php
// CREATE NewFeatureTest.php with these test cases:
public function testGivenAValidInputWhenIExecuteTheFeatureThenASuccessMessageIsPrinted(): void
{
    // Arrange
    $featureClass = $this->tester->get(NewFeature::class);

    // Act
    $result = $featureClass->execute('valid_input');

    // Assert
    $this->assertEquals('success', $result->getStatus());
}

public function testGivenAnInvalidInputWhenIExecuteTheFeatureThenAnExceptionIsThrown(): void
{
    // Arrange
    $featureClass = $this->tester->get(NewFeature::class);

    // Expect
    $this->expectException(ValidationException::class);

    // Act
    $featureClass->execute('invalid_input');
}

public function testGivenAValidInputWhenIExecuteTheFeatureAndTheUnerlyingServiceTimeoutThenAnErrorMessageIsPrinted(): void
{
    // Arrange
    // Handles timeouts gracefully
    // Mock external API to throw timeout
    $mockApi = $this->createMock(ExternalApiInterface::class);
    $mockApi->method('call')->willThrowException(new TimeoutException());

    $this->tester->set(ExternalApiInterface::class, $mockApi);

    $featureClass = $this->tester->get(NewFeature::class);

    // Act
    $result = $featureClass->execute('valid_input');
    
    $this->assertEquals('error', $result->getStatus());
}
```

```bash
# Run and iterate until passing:
composer test
# If failing: Read error, understand root cause, fix code, re-run (never mock to pass)
```

### Level 3: Integration Test

```bash
# Test the console command
php bin/console app:new-feature --input="test_value"

# Expected: Command executes successfully with proper output
# If error: Check logs in var/cache/test/ for stack trace
```

### Level 4: Deployment & Creative Validation

```bash
# MCP servers or other creative validation methods
# Examples:
# - Load testing with realistic data
# - End-to-end user journey testing
# - Performance benchmarking
# - Security scanning
# - Documentation validation

# Custom validation specific to the feature
# [Add creative validation methods here]
```

## Final validation Checklist

- [ ] All tests pass: `composer test`
- [ ] No linting errors: `composer cs-check`
- [ ] No type errors: `composer phpstan`
- [ ] Error cases handled gracefully
- [ ] Logs are informative but not verbose
- [ ] Documentation updated if needed

---

## Anti-Patterns to Avoid

- ❌ Don't create new patterns when existing ones work
- ❌ Don't skip validation because "it should work"
- ❌ Don't ignore failing tests - fix them
- ❌ Don't mix different dependency injection patterns
- ❌ Don't hardcode values that should be config
- ❌ Don't catch all exceptions - be specific
