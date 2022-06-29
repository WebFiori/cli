<?php
namespace webfiori\tests\cli\testCommands;

use webfiori\cli\CLICommand;
/**
 * Description of Command00
 *
 * @author i.binalshikh
 */
class Command00 extends CLICommand {

    public function __construct() {
        parent::__construct('super-hero', [
            'name' => [
                'values' => [
                    'Ibrahim', 'Ali'
                ]
            ]
        ], 'A command to display hero\'s name.');
    }

    public function exec(): int {
        $hero = $this->getArgValue('name');
        $this->println("Hello hero %s", $hero);
        return 0;
    }

}
