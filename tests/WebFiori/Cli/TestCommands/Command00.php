<?php
namespace WebFiori\Tests\Cli\TestCommands;

use WebFiori\Cli\CliCommand;
/**
 * Description of Command00
 *
 * @author i.binalshikh
 */
class Command00 extends CliCommand {

    public function __construct() {
        parent::__construct('super-hero', [
            'name' => [
                'values' => [
                    'Ibrahim', 'Ali'
                ],
                'description' => 'The name of the hero'
            ]
        ], 'A command to display hero\'s name.');
    }

    public function exec(): int {
        $hero = $this->getArgValue('name');
        $this->println("Hello hero %s", $hero);
        return 0;
    }

}
