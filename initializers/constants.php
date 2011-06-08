<?php

namespace Phuby {
    /**
     * [QUIRK] Cannot use "const FOO = 'BAR'" syntax with string concatenation or functions
     *         e.g. "const ROOT = dirname(__DIR__).DIRECTORY_SEPARATOR.'lib';"
     *
     * [QUIRK] Namespaces are downcased in get_defined_constants() keys when constants are
     *         defined with the "const" keyword
    **/
    define(__NAMESPACE__.'\\NS',      '\\');
    define(__NAMESPACE__.NS.'__NS__', __NAMESPACE__.NS);
    define(__NS__.'DS',               DIRECTORY_SEPARATOR);
    define(__NS__.'PS',               PATH_SEPARATOR);
    define(__NS__.'ROOT',             dirname(__DIR__).DS.'lib');
    define(__NS__.'VERSION',          '0.0.0');

    /**
     * [QUIRK] Constants defined in the Phuby namespace are not resolved when called from subnamespaces
     *         so we need to manually define them for use in the Phuby\Core namespace
    **/
    $constants = get_defined_constants(true);
    foreach ($constants['user'] as $name => $value) {
        if (preg_match('#^'.preg_quote(__NS__).'(.+)$#', $name, $matches)) {
            define(__NS__.'Core'.NS.$matches[1], $value);
        }
    }
}