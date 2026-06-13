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
namespace WebFiori\Cli;

/**
 * Constants that define the verbosity levels for CLI output.
 *
 * @author Ibrahim
 */
class Verbosity {
    /**
     * Debug mode - maximum output detail.
     */
    const DEBUG = 3;
    /**
     * Normal mode - default output level.
     */
    const NORMAL = 1;
    /**
     * Quiet mode - only errors and warnings are shown.
     */
    const QUIET = 0;
    /**
     * Verbose mode - additional diagnostic information.
     */
    const VERBOSE = 2;
}
