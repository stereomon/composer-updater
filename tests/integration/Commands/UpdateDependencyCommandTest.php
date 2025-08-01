<?php

declare(strict_types=1);

namespace Tests\Integration\Commands;

use App\Commands\UpdateDependencyCommand;
use App\Shared\Transfer\ErrorTransfer;
use App\Shared\Transfer\UpdateDependencyResponseTransfer;
use Codeception\Test\Unit;
use IntegrationTester;

class UpdateDependencyCommandTest extends Unit
{
    protected IntegrationTester $tester;

    public function testGivenValidArgumentsWhenIRunTheUpdateDependencyCommandThenASuccessMessageIsDisplayed(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(
            UpdateDependencyCommand::class
        );

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'module1,module2',
            'package-name' => 'symfony/console',
            'version' => '^6.0',
            'description' => 'Update console component'
        ]);

        // Assert
        $this->assertStringContainsString(
            'Updated 0 modules with symfony/console version ^6.0',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenValidArgumentsWithoutDescriptionWhenIRunTheCommandThenSuccessMessageIsShown(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(
            UpdateDependencyCommand::class
        );

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'module1',
            'package-name' => 'symfony/console',
            'version' => '^6.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'Updated 0 modules with symfony/console version ^6.0',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenSingleModuleWhenIRunTheUpdateDependencyCommandThenTheModuleIsProcessed(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(
            UpdateDependencyCommand::class
        );

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'single-module',
            'package-name' => 'monolog/monolog',
            'version' => '^3.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'Updated 0 modules with monolog/monolog version ^3.0',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenMultipleModulesWithSpacesWhenIRunTheCommandThenAllModulesAreProcessed(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(UpdateDependencyCommand::class);

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => ' module1 , module2 , module3 ',
            'package-name' => 'phpunit/phpunit',
            'version' => '^10.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'Updated 0 modules with phpunit/phpunit version ^10.0',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenEmptyModulesStringWhenIRunTheCommandThenErrorMessageIsShown(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(UpdateDependencyCommand::class);

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => '',
            'package-name' => 'symfony/console',
            'version' => '^6.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'At least one module name must be provided',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenOnlyCommasInModulesWhenIRunTheCommandThenErrorMessageIsShown(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(UpdateDependencyCommand::class);

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => ',,,',
            'package-name' => 'symfony/console',
            'version' => '^6.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'At least one module name must be provided',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenOnlySpacesInModulesWhenIRunTheCommandThenErrorMessageIsShown(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(UpdateDependencyCommand::class);

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => '   ',
            'package-name' => 'symfony/console',
            'version' => '^6.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'At least one module name must be provided',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenModulesWithEmptyElementsWhenIRunTheCommandThenOnlyValidModulesAreProcessed(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(UpdateDependencyCommand::class);

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'module1,,module2, ,module3',
            'package-name' => 'doctrine/orm',
            'version' => '^2.15'
        ]);

        // Assert
        $this->assertStringContainsString(
            'Updated 0 modules with doctrine/orm version ^2.15',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenPackageNameWithSpecialCharactersWhenIRunTheCommandThenItIsProcessedCorrectly(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(UpdateDependencyCommand::class);

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'test-module',
            'package-name' => 'vendor/package-name_with-special.chars',
            'version' => '^1.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'Updated 0 modules with vendor/package-name_with-special.chars version ^1.0',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenVersionWithComplexConstraintsWhenIRunTheCommandThenItIsProcessedCorrectly(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(UpdateDependencyCommand::class);

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'complex-module',
            'package-name' => 'symfony/framework-bundle',
            'version' => '>=5.4,<7.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'Updated 0 modules with symfony/framework-bundle version >=5.4,<7.0',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenLongDescriptionWhenIRunTheCommandThenItIsHandledCorrectly(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(UpdateDependencyCommand::class);
        $longDescription = str_repeat('This is a very long description. ', 50);

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'description-test-module',
            'package-name' => 'test/package',
            'version' => '^1.0',
            'description' => $longDescription
        ]);

        // Assert
        $this->assertStringContainsString(
            'Updated 0 modules with test/package version ^1.0',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenMockedServiceReturningErrorsWhenIRunCommandThenErrorsAreDisplayed(): void
    {
        // Arrange
        $errors = [
            new ErrorTransfer('Module not found'),
            new ErrorTransfer('Invalid package name'),
        ];

        $mockAppFacade = $this->createMock(\App\AppFacade::class);
        $mockAppFacade->method('updateDependency')
            ->willReturn(new UpdateDependencyResponseTransfer(false, [], null, $errors));

        $updateDependencyCommand = new UpdateDependencyCommand($mockAppFacade);
        $updateDependencyCommandTester = $this->tester->createCommandTester($updateDependencyCommand);

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'test-module',
            'package-name' => 'test/package',
            'version' => '^1.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'The following errors occurred:',
            $updateDependencyCommandTester->getDisplay()
        );
        $this->assertStringContainsString('• Module not found', $updateDependencyCommandTester->getDisplay());
        $this->assertStringContainsString('• Invalid package name', $updateDependencyCommandTester->getDisplay());
    }

    public function testGivenMockedServiceReturningNullUpdateMessageWhenIRunCommandThenErrorIsDisplayed(): void
    {
        // Arrange
        $mockAppFacade = $this->createMock(\App\AppFacade::class);
        $mockAppFacade->method('updateDependency')
            ->willReturn(new UpdateDependencyResponseTransfer(true, ['module1'], null));

        $updateDependencyCommand = new UpdateDependencyCommand($mockAppFacade);
        $updateDependencyCommandTester = $this->tester->createCommandTester(
            $updateDependencyCommand
        );

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'test-module',
            'package-name' => 'test/package',
            'version' => '^1.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'No update message was generated',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenSuccessfulUpdateWithModulesListWhenIRunCommandThenUpdatedModulesAreShown(): void
    {
        // Arrange
        $updatedModules = ['module1', 'module2', 'module3'];
        $updateMessage = 'Successfully updated dependencies';

        $mockAppFacade = $this->createMock(\App\AppFacade::class);
        $mockAppFacade->method('updateDependency')
            ->willReturn(new UpdateDependencyResponseTransfer(true, $updatedModules, $updateMessage));

        $updateDependencyCommand = new UpdateDependencyCommand($mockAppFacade);
        $updateDependencyCommandTester = $this->tester->createCommandTester(
            $updateDependencyCommand
        );

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'module1,module2,module3',
            'package-name' => 'test/package',
            'version' => '^1.0'
        ]);

        // Assert
        $this->assertStringContainsString($updateMessage, $updateDependencyCommandTester->getDisplay());
        $this->assertStringContainsString('Updated Modules:', $updateDependencyCommandTester->getDisplay());
        $this->assertStringContainsString('module1', $updateDependencyCommandTester->getDisplay());
        $this->assertStringContainsString('module2', $updateDependencyCommandTester->getDisplay());
        $this->assertStringContainsString('module3', $updateDependencyCommandTester->getDisplay());
    }

    public function testGivenSuccessfulUpdateWithoutModulesListWhenIRunCommandThenOnlyMessageIsShown(): void
    {
        // Arrange
        $updateMessage = 'No modules were updated';

        $mockAppFacade = $this->createMock(\App\AppFacade::class);
        $mockAppFacade->method('updateDependency')
            ->willReturn(new UpdateDependencyResponseTransfer(true, [], $updateMessage));

        $updateDependencyCommand = new UpdateDependencyCommand($mockAppFacade);
        $updateDependencyCommandTester = $this->tester->createCommandTester(
            $updateDependencyCommand
        );

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'test-module',
            'package-name' => 'test/package',
            'version' => '^1.0'
        ]);

        // Assert
        $this->assertStringContainsString($updateMessage, $updateDependencyCommandTester->getDisplay());
        $this->assertStringNotContainsString('Updated Modules:', $updateDependencyCommandTester->getDisplay());
    }

    public function testGivenMockedServiceWithSingleErrorWhenIRunCommandThenSingleErrorIsDisplayed(): void
    {
        // Arrange
        $errors = [
            new ErrorTransfer('Single error message'),
        ];

        $mockAppFacade = $this->createMock(\App\AppFacade::class);
        $mockAppFacade->method('updateDependency')
            ->willReturn(new UpdateDependencyResponseTransfer(false, [], null, $errors));

        $updateDependencyCommand = new UpdateDependencyCommand($mockAppFacade);
        $updateDependencyCommandTester = $this->tester->createCommandTester(
            $updateDependencyCommand
        );

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'test-module',
            'package-name' => 'test/package',
            'version' => '^1.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'The following errors occurred:',
            $updateDependencyCommandTester->getDisplay()
        );
        $this->assertStringContainsString('• Single error message', $updateDependencyCommandTester->getDisplay());
    }

    public function testGivenCommandConfigurationWhenAccessedThenAllArgumentsAreConfigured(): void
    {
        // Arrange
        $updateDependencyCommand = new UpdateDependencyCommand(
            $this->tester->get(\App\AppFacade::class)
        );

        // Act & Assert - this will trigger the configure() method
        $definition = $updateDependencyCommand->getDefinition();

        $this->assertTrue($definition->hasArgument('modules'));
        $this->assertTrue($definition->getArgument('modules')->isRequired());

        $this->assertTrue($definition->hasArgument('package-name'));
        $this->assertTrue($definition->getArgument('package-name')->isRequired());

        $this->assertTrue($definition->hasArgument('version'));
        $this->assertTrue($definition->getArgument('version')->isRequired());

        $this->assertTrue($definition->hasArgument('description'));
        $this->assertFalse($definition->getArgument('description')->isRequired());
    }

    public function testGivenMockedServiceWithMultipleErrorsWhenIRunCommandThenAllErrorsAreDisplayed(): void
    {
        // Arrange
        $errors = [
            new ErrorTransfer('First error message'),
            new ErrorTransfer('Second error message'),
            new ErrorTransfer('Third error message'),
            new ErrorTransfer('Fourth error message'),
        ];

        $mockAppFacade = $this->createMock(\App\AppFacade::class);
        $mockAppFacade->method('updateDependency')
            ->willReturn(new UpdateDependencyResponseTransfer(false, [], null, $errors));

        $updateDependencyCommand = new UpdateDependencyCommand($mockAppFacade);
        $updateDependencyCommandTester = $this->tester->createCommandTester(
            $updateDependencyCommand
        );

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => 'test-module',
            'package-name' => 'test/package',
            'version' => '^1.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'The following errors occurred:',
            $updateDependencyCommandTester->getDisplay()
        );
        $this->assertStringContainsString('• First error message', $updateDependencyCommandTester->getDisplay());
        $this->assertStringContainsString('• Second error message', $updateDependencyCommandTester->getDisplay());
        $this->assertStringContainsString('• Third error message', $updateDependencyCommandTester->getDisplay());
        $this->assertStringContainsString('• Fourth error message', $updateDependencyCommandTester->getDisplay());
    }

    public function testGivenModulesWithUnicodeCharactersWhenIRunTheCommandThenTheyAreProcessedCorrectly(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(UpdateDependencyCommand::class);

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => '测试模块,тест-модуль',
            'package-name' => 'test/unicode-package',
            'version' => '^1.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'Updated 0 modules with test/unicode-package version ^1.0',
            $updateDependencyCommandTester->getDisplay()
        );
    }

    public function testGivenVeryLongModuleNamesWhenIRunTheCommandThenTheyAreHandledCorrectly(): void
    {
        // Arrange
        $updateDependencyCommandTester = $this->tester->getCommandTester(UpdateDependencyCommand::class);
        $longModuleName = str_repeat('very-long-module-name-', 10);

        // Act
        $updateDependencyCommandTester->execute([
            'modules' => $longModuleName,
            'package-name' => 'test/package',
            'version' => '^1.0'
        ]);

        // Assert
        $this->assertStringContainsString(
            'Updated 0 modules with test/package version ^1.0',
            $updateDependencyCommandTester->getDisplay()
        );
    }
}
