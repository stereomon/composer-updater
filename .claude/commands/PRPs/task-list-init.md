claude
** Create a comprehensive task list in PRPs/checklist.md for building our hackathon project based on $ARGIMENTS

Ingest the information then dig deep into our existing codebase, When done ->

ULTRATHINK about the product task and create the plan based on CLAUDE.md and create detailed tasks following this principle:

### list of tasks to be completed to fullfill the PRP in the order they should be completed using infomration dense keywords

 - Information dense keyword examples:
 ADD, CREATE, MODIFY, MIRROR, FIND, EXECUTE, KEEP, PRESERVE etc

 Mark done tasks with: STATUS [DONE], if not done leave empty

```yaml
Task 1:
STATUS [ ]
MODIFY src/ExistingModule.php:
  - FIND pattern: "class OldImplementation"
  - INJECT after line containing "public function __construct"
  - PRESERVE existing method signatures

STATUS [ ]
CREATE src/NewFeature.php:
  - MIRROR pattern from: src/SimilarFeature.php
  - MODIFY class name and core logic
  - KEEP error handling pattern identical

...(...)

Task N:
...

```

Each task should have integration test coverage, ensure tests pass on each task. Remember that we are testing the console command as entry point with all possible paths. This will cover the used code behind the facade.