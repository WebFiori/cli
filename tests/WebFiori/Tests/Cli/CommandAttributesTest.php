<?php
/**
 * This file is licensed under MIT License.
 *
 * Copyright (c) 2026-present Webfiori Framework
 *
 * For more information on the license, please visit:
 * https://github.com/WebFiori/.github/blob/main/LICENSE
 *
 */

declare(strict_types=1);

namespace WebFiori\Tests\Cli;

use WebFiori\Cli\Attributes\Group;
use WebFiori\Cli\Attributes\SingleInstance;
use WebFiori\Cli\Command;
use WebFiori\Cli\CommandTestCase;
use WebFiori\Cli\Runner;

#[SingleInstance]
class SingleInstanceCommand extends Command {
    public function __construct() {
        parent::__construct('locked-cmd', [], 'A single-instance command');
    }

    public function exec(): int {
        $this->println('running');

        return 0;
    }
}

#[SingleInstance(lockPath: null, exitCode: 42)]
class SingleInstanceCustomExitCommand extends Command {
    public function __construct() {
        parent::__construct('locked-custom', [], 'Custom exit code');
    }

    public function exec(): int {
        $this->println('running');

        return 0;
    }
}

#[Group('db')]
class DbMigrateCommand extends Command {
    public function __construct() {
        parent::__construct('db:migrate', [], 'Run database migrations');
    }

    public function exec(): int {
        $this->println('migrating');

        return 0;
    }
}

#[Group('db')]
class DbSeedCommand extends Command {
    public function __construct() {
        parent::__construct('db:seed', [], 'Seed the database');
    }

    public function exec(): int {
        return 0;
    }
}

class UngroupedCommand extends Command {
    public function __construct() {
        parent::__construct('serve', [], 'Start dev server');
    }

    public function exec(): int {
        return 0;
    }
}

class ColonGroupCommand extends Command {
    public function __construct() {
        parent::__construct('cache:clear', [], 'Clear the cache');
    }

    public function exec(): int {
        return 0;
    }
}

class CommandAttributesTest extends CommandTestCase {
    /**
     * @test
     */
    public function testSingleInstanceRunsNormally() {
        $output = $this->executeSingleCommand(new SingleInstanceCommand());
        $this->assertContains("running\n", $output);
        $this->assertEquals(0, $this->getExitCode());
    }

    /**
     * @test
     */
    public function testSingleInstanceBlocksConcurrent() {
        // Acquire lock manually, then try to run the command
        $lm = new \WebFiori\Cli\LockManager();
        $this->assertTrue($lm->acquire('locked-cmd'));

        $output = $this->executeSingleCommand(new SingleInstanceCommand());
        $outputStr = implode('', $output);

        $this->assertStringContainsString('already running', $outputStr);
        $this->assertEquals(1, $this->getExitCode());

        $lm->release();
    }

    /**
     * @test
     */
    public function testSingleInstanceCustomExitCode() {
        $lm = new \WebFiori\Cli\LockManager();
        $this->assertTrue($lm->acquire('locked-custom'));

        $this->executeSingleCommand(new SingleInstanceCustomExitCommand());
        $this->assertEquals(42, $this->getExitCode());

        $lm->release();
    }

    /**
     * @test
     */
    public function testSingleInstanceReleasesLockAfterExec() {
        $this->executeSingleCommand(new SingleInstanceCommand());

        // Lock should be released — can acquire again
        $lm = new \WebFiori\Cli\LockManager();
        $this->assertTrue($lm->acquire('locked-cmd'));
        $lm->release();
    }

    /**
     * @test
     */
    public function testGroupAttributeResolved() {
        $cmd = new DbMigrateCommand();
        $runner = new Runner();
        $runner->reset();
        $runner->register($cmd);

        $this->assertEquals('db', $cmd->getGroup());
    }

    /**
     * @test
     */
    public function testGroupFromColonConvention() {
        $cmd = new ColonGroupCommand();
        $runner = new Runner();
        $runner->reset();
        $runner->register($cmd);

        $this->assertEquals('cache', $cmd->getGroup());
    }

    /**
     * @test
     */
    public function testExplicitGroupOverridesAttribute() {
        $cmd = new DbMigrateCommand();
        $cmd->setGroup('custom');
        $runner = new Runner();
        $runner->reset();
        $runner->register($cmd);

        // Explicit setGroup should win
        $this->assertEquals('custom', $cmd->getGroup());
    }

    /**
     * @test
     */
    public function testUngroupedCommandHasNullGroup() {
        $cmd = new UngroupedCommand();
        $runner = new Runner();
        $runner->reset();
        $runner->register($cmd);

        $this->assertNull($cmd->getGroup());
    }

    /**
     * @test
     */
    public function testHelpOutputGrouped() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new DbMigrateCommand());
        $runner->register(new DbSeedCommand());
        $runner->register(new UngroupedCommand());
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'help']);
        $runner->start();

        $output = $runner->getOutput();
        $outputStr = implode('', $output);

        // Should show group header
        $this->assertStringContainsString('db:', $outputStr);
        // Should contain commands
        $this->assertStringContainsString('db:migrate', $outputStr);
        $this->assertStringContainsString('db:seed', $outputStr);
        $this->assertStringContainsString('serve', $outputStr);
    }

    /**
     * @test
     */
    public function testHelpOutputNoGroupsFlatList() {
        $runner = new Runner();
        $runner->reset();
        $runner->register(new UngroupedCommand());
        $runner->setInputs([]);
        $runner->setArgsVector(['main.php', 'help']);
        $runner->start();

        $output = $runner->getOutput();
        $outputStr = implode('', $output);

        // No group headers for ungrouped-only output
        $this->assertStringContainsString('serve', $outputStr);
    }

    /**
     * @test
     */
    public function testGetGroupDefaultNull() {
        $cmd = new UngroupedCommand();
        $this->assertNull($cmd->getGroup());
    }

    /**
     * @test
     */
    public function testSetGroup() {
        $cmd = new UngroupedCommand();
        $cmd->setGroup('mygroup');
        $this->assertEquals('mygroup', $cmd->getGroup());
    }
}
