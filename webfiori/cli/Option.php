<?php

namespace webfiori\cli;

/**
 * A class that holds the options which are used to configure command line argument.
 *
 * @author Ibrahim
 */
class Option {
    /**
     * An option which is used to tell if the argument is optional or not.
     * Accepts 'true' or 'false' as its value.
     */
    const OPTIONAL = 'optional';
    /**
     * An option which is used to set a default value for the argument if not
     * provided and it was optional.
     */
    const DEFAULT = 'default';
    /**
     * Help text of the argument. Used when the command 'help' is executed.
     */
    const DESCRIPTION = 'description';
    /**
     * An array of values at which the argument can accept. Used to restrict
     * the values that can be supplied to the argument.
     */
    const VALUES = 'values';
}
