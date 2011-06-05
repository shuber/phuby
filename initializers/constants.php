<?php

namespace Phuby {
    const DS = DIRECTORY_SEPARATOR;
    const NS = '\\';
    const PS = PATH_SEPARATOR;
    const VERSION = '0.0.0';

    // [QUIRK] Cannot use "const FOO = 'BAR'" syntax with string concatenation
    //         e.g. "const __NS__ = __NAMESPACE__.NS;"
    define(__NAMESPACE__.NS.'__NS__', __NAMESPACE__.NS);
    define(__NS__.'ROOT', dirname(__DIR__).DS.'lib');
}