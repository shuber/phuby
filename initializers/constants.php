<?php

namespace Phuby {
    /**
     * [QUIRK] Cannot use "const FOO = 'BAR'" syntax with string concatenation or functions
     *         e.g. "const ROOT = dirname(__DIR__).DIRECTORY_SEPARATOR.'lib';"
     *
     * [QUIRK] Namespaces returned from get_defined_constants() are downcased when constants
     *         are defined with the "const" keyword
    **/
    define(__NAMESPACE__.'\\NS',        '\\');
    define(__NAMESPACE__.NS.'__NS__',   __NAMESPACE__.NS);
    define(__NAMESPACE__.NS.'__CORE__', __NS__.'Core'.NS);
    define(__NS__.'DS',                 DIRECTORY_SEPARATOR);
    define(__NS__.'PS',                 PATH_SEPARATOR);
    define(__NS__.'ROOT',               dirname(__DIR__).DS.'lib');
    define(__NS__.'VERSION',            '0.0.0');

    /**
     * [QUIRK] Constants defined in the Phuby namespace are not resolved when called from subnamespaces
     *         so we need to manually copy them for use in the Phuby\Core namespace
    **/
    $constants = get_defined_constants(true);
    foreach ($constants['user'] as $name => $value) {
        preg_match('#^'.preg_quote(__NS__).'(.+)$#', $name, $matches);
        if ($matches) define(__CORE__.$matches[1], $value);
    }
}