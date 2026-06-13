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
namespace WebFiori\Cli\Attributes;

use Attribute;

/**
 * Attribute to assign a command to a named group for help display organization.
 *
 * @author Ibrahim
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Group {
    public readonly string $name;

    public function __construct(string $name) {
        $this->name = $name;
    }
}
