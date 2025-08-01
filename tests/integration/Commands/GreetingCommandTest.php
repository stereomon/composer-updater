<?php

declare(strict_types=1);

namespace Tests\Integration\Commands;

use App\Commands\GreetingCommand;
use Codeception\Test\Unit;
use IntegrationTester;

class GreetingCommandTest extends Unit
{
    protected IntegrationTester $tester;

    public function testGivenAValidNameWhenIRunTheGreetingCommandThenAGreetingMessageIsDisplayed(): void
    {
        // Arrange
        $greetingCommandTester = $this->tester->getCommandTester(GreetingCommand::class);

        // Act
        $greetingCommandTester->execute(['name' => 'John']);

        // Assert
        $this->assertStringContainsString('Hello John!', $greetingCommandTester->getDisplay());
    }

    public function testGivenAnEmptyNameWhenIRunTheGreetingCommandThenTheErrorMessageIsShown(): void
    {
        // Arrange
        $greetingCommandTester = $this->tester->getCommandTester(GreetingCommand::class);

        // Act
        $greetingCommandTester->execute(['name' => '']);

        // Assert
        $this->assertStringContainsString('The following errors occurred:', $greetingCommandTester->getDisplay());
    }

    public function testGivenAnEmptyNameWhenIRunTheGreetingCommandThenTheSpecificErrorMessageIsShown(): void
    {
        // Arrange
        $greetingCommandTester = $this->tester->getCommandTester(GreetingCommand::class);

        // Act
        $greetingCommandTester->execute(['name' => '']);

        // Assert
        $this->assertStringContainsString('Name cannot be empty', $greetingCommandTester->getDisplay());
    }

    public function testGivenAWhitespaceOnlyNameWhenIRunTheGreetingCommandThenTheEmptyNameErrorIsShown(): void
    {
        // Arrange
        $greetingCommandTester = $this->tester->getCommandTester(GreetingCommand::class);

        // Act
        $greetingCommandTester->execute(['name' => '   ']);

        // Assert
        $this->assertStringContainsString('Name cannot be empty', $greetingCommandTester->getDisplay());
    }

    public function testGivenANameWithSpecialCharactersWhenIRunTheGreetingCommandThenTheCorrectGreetingIsShown(): void
    {
        // Arrange
        $greetingCommandTester = $this->tester->getCommandTester(GreetingCommand::class);

        // Act
        $greetingCommandTester->execute(['name' => 'José-María']);

        // Assert
        $this->assertStringContainsString('Hello José-María!', $greetingCommandTester->getDisplay());
    }

    public function testGivenAVeryLongNameWhenIRunTheGreetingCommandThenTheCorrectGreetingIsShown(): void
    {
        // Arrange
        $greetingCommandTester = $this->tester->getCommandTester(GreetingCommand::class);
        $longName = str_repeat('A', 100);

        // Act
        $greetingCommandTester->execute(['name' => $longName]);

        // Assert
        $this->assertStringContainsString("Hello {$longName}!", $greetingCommandTester->getDisplay());
    }

    public function testGivenANameWithNumbersWhenIRunTheGreetingCommandThenTheCorrectGreetingIsShown(): void
    {
        // Arrange
        $greetingCommandTester = $this->tester->getCommandTester(GreetingCommand::class);

        // Act
        $greetingCommandTester->execute(['name' => 'John123']);

        // Assert
        $this->assertStringContainsString('Hello John123!', $greetingCommandTester->getDisplay());
    }

    public function testGivenANameWithTabsAndSpacesWhenIRunTheGreetingCommandThenTheEmptyNameErrorIsShown(): void
    {
        // Arrange
        $greetingCommandTester = $this->tester->getCommandTester(GreetingCommand::class);

        // Act
        $greetingCommandTester->execute(['name' => "\t\n  \r"]);

        // Assert
        $this->assertStringContainsString('Name cannot be empty', $greetingCommandTester->getDisplay());
    }

    public function testGivenANameWithLeadingTrailingSpacesWhenIRunCommandThenCorrectGreetingIsShown(): void
    {
        // Arrange
        $greetingCommandTester = $this->tester->getCommandTester(GreetingCommand::class);

        // Act
        $greetingCommandTester->execute(['name' => '  John  ']);

        // Assert
        $this->assertStringContainsString('Hello   John  !', $greetingCommandTester->getDisplay());
    }

    public function testGivenANameWithUnicodeCharactersWhenIRunTheGreetingCommandThenTheCorrectGreetingIsShown(): void
    {
        // Arrange
        $greetingCommandTester = $this->tester->getCommandTester(GreetingCommand::class);

        // Act
        $greetingCommandTester->execute(['name' => '田中太郎']);

        // Assert
        $this->assertStringContainsString('Hello 田中太郎!', $greetingCommandTester->getDisplay());
    }

    public function testGivenANameWithEmojiWhenIRunTheGreetingCommandThenTheCorrectGreetingIsShown(): void
    {
        // Arrange
        $greetingCommandTester = $this->tester->getCommandTester(GreetingCommand::class);

        // Act
        $greetingCommandTester->execute(['name' => 'John😊']);

        // Assert
        $this->assertStringContainsString('Hello John😊!', $greetingCommandTester->getDisplay());
    }

    public function testGivenAMockedServiceReturningNullGreetingWhenIRunCommandThenErrorIsDisplayed(): void
    {
        // Arrange
        $mockGreetingService = $this->createMock(\App\Greeting\GreetingService::class);
        $mockGreetingService->method('greetUser')
            ->willReturn(new \App\Shared\Transfer\GreetingResponseTransfer(true, null));

        $mockAppFacade = $this->createMock(\App\AppFacade::class);
        $mockAppFacade->method('greetUser')
            ->willReturn(new \App\Shared\Transfer\GreetingResponseTransfer(true, null));

        $greetingCommand = new \App\Commands\GreetingCommand($mockAppFacade);
        $greetingCommandTester = $this->tester->createCommandTester($greetingCommand);

        // Act
        $greetingCommandTester->execute(['name' => 'John']);

        // Assert
        $this->assertStringContainsString('No greeting message was generated', $greetingCommandTester->getDisplay());
    }

    public function testGivenAMockedServiceWithMultipleErrorsWhenIRunCommandThenAllErrorsAreDisplayed(): void
    {
        // Arrange
        $errors = [
            new \App\Shared\Transfer\ErrorTransfer('First error message'),
            new \App\Shared\Transfer\ErrorTransfer('Second error message'),
            new \App\Shared\Transfer\ErrorTransfer('Third error message'),
        ];

        $mockAppFacade = $this->createMock(\App\AppFacade::class);
        $mockAppFacade->method('greetUser')
            ->willReturn(new \App\Shared\Transfer\GreetingResponseTransfer(false, null, $errors));

        $greetingCommand = new \App\Commands\GreetingCommand($mockAppFacade);
        $greetingCommandTester = $this->tester->createCommandTester($greetingCommand);

        // Act
        $greetingCommandTester->execute(['name' => 'John']);

        // Assert
        $this->assertStringContainsString('The following errors occurred:', $greetingCommandTester->getDisplay());
        $this->assertStringContainsString('• First error message', $greetingCommandTester->getDisplay());
        $this->assertStringContainsString('• Second error message', $greetingCommandTester->getDisplay());
        $this->assertStringContainsString('• Third error message', $greetingCommandTester->getDisplay());
    }

    public function testGivenCommandConfigurationWhenAccessedThenArgumentIsConfigured(): void
    {
        // Arrange
        $greetingCommand = new \App\Commands\GreetingCommand(
            $this->tester->get(\App\AppFacade::class)
        );

        // Act & Assert - this will trigger the configure() method
        $definition = $greetingCommand->getDefinition();
        $this->assertTrue($definition->hasArgument('name'));
        $this->assertTrue($definition->getArgument('name')->isRequired());
    }
}
