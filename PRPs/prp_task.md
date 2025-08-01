---
Intended for Jira/GitHub tasks or other task management systems to break down and plan the implementation.
---

# Task Template v2 - Information Dense with Validation Loops

> Concise, executable tasks with embedded context and validation commands

## Format

```
[ACTION] path/to/file:
  - [OPERATION]: [DETAILS]
  - VALIDATE: [COMMAND]
  - IF_FAIL: [DEBUG_HINT]
```

## Actions keywords to use when creating tasks for concise and meaningful descriptions

- **READ**: Understand existing patterns
- **CREATE**: New file with specific content
- **UPDATE**: Modify existing file
- **DELETE**: Remove file/code
- **FIND**: Search for patterns
- **TEST**: Verify behavior
- **FIX**: Debug and repair

## Critical Context Section

```yaml
# Include these BEFORE tasks when context is crucial
context:
  docs:
    - url: [API documentation]
      focus: [specific method/section]

  patterns:
    - file: existing/Example.php
      copy: [pattern name]

  gotchas:
    - issue: "Library X requires Y"
      fix: "Always do Z first"
```

## Task Examples with Validation

### Setup Tasks

```
READ config/services.php:
  - UNDERSTAND: Current configuration structure
  - FIND: Model configuration pattern
  - NOTE: Config uses pydantic BaseSettings

READ tests/unit/ModelsTest.php:
  - UNDERSTAND: Test pattern for models
  - FIND: Fixture setup approach
  - NOTE: Uses Codeception for unit and integration tests
```

### Implementation Tasks

````
UPDATE path/to/file:
  - FIND: $serviceRegistry = [
  - ADD: "new-service" => NewServiceClass::class,
  - VALIDATE: php -r "require 'vendor/autoload.php'; $registry = require 'path/to/file'; assert(array_key_exists('new-service', $registry));"
  - IF_FAIL: Check namespace and class name for NewServiceClass

CREATE path/to/file:
  - COPY_PATTERN: path/to/other/file
  - IMPLEMENT:
   - [Detailed description of what needs to be implemented based on codebase intelligence]
  - VALIDATE: composer test path/to/file

UPDATE path/to/file:
  - FIND: $container->register(
  - ADD_AFTER:
    ```php
    use App\Commands\NewModelCommand;
    $container->register(NewModelCommand::class)
        ->addTag('console.command');
    ```
  - VALIDATE: composer test path/to/file
````

## Validation Checkpoints

```
CHECKPOINT syntax:
  - RUN: composer cs-fix && composer phpstan
  - FIX: Any reported issues
  - CONTINUE: Only when clean

CHECKPOINT tests:
  - RUN: composer test path/to/file
  - REQUIRE: All passing
  - DEBUG: vendor/bin/codecept run unit path/to/file/FailingTest.php --debug
  - CONTINUE: Only when all green

CHECKPOINT integration:
  - START: php bin/console cache:clear --env=test
  - RUN: composer test
  - EXPECT: "All tests passed"
  - CLEANUP: php bin/console cache:clear
```

## Debug Patterns

```
DEBUG autoload_error:
  - CHECK: File exists at path
  - CHECK: Namespace matches directory structure
  - TRY: php -r "require 'vendor/autoload.php'; new App\Path\To\Class();"
  - FIX: Run composer dump-autoload or fix namespace

DEBUG test_failure:
  - RUN: vendor/bin/codecept run unit path/to/test.php::testName --debug
  - ADD: $this->debug("Debug: " . print_r($variable, true));
  - IDENTIFY: Assertion vs implementation issue
  - FIX: Update test or fix code

DEBUG command_error:
  - CHECK: Command registered in services.php
  - TEST: php bin/console list
  - READ: Console output for error details
  - FIX: Based on specific error
```

## Common Task examples

### Add New Feature

```
1. READ existing similar feature
2. CREATE new feature file (COPY pattern)
3. UPDATE registry/router to include
4. CREATE tests for feature
5. TEST all tests pass
6. FIX any linting/type issues
7. TEST integration works
```

### Fix Bug

```
1. CREATE failing test that reproduces bug
2. TEST confirm test fails
3. READ relevant code to understand
4. UPDATE code with fix
5. TEST confirm test now passes
6. TEST no other tests broken
7. UPDATE changelog
```

### Refactor Code

```
1. TEST current tests pass (baseline)
2. CREATE new structure (don't delete old yet)
3. UPDATE one usage to new structure
4. TEST still passes
5. UPDATE remaining usages incrementally
6. DELETE old structure
7. TEST full suite passes
```

## Tips for Effective Tasks

- Use VALIDATE after every change
- Include IF_FAIL hints for common issues
- Reference specific line numbers or patterns
- Keep validation commands simple and fast
- Chain related tasks with clear dependencies
- Always include rollback/undo steps for risky changes
