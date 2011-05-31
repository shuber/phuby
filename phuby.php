<?php

namespace Phuby {
    const DS = DIRECTORY_SEPARATOR;
    const NS = '\\';
    const PS = PATH_SEPARATOR;
    const VERSION = '0.0.0';

    // [QUIRK] Cannot use "const FOO = 'BAR'" syntax with string concatenation e.g. "const __NS__ = __NAMESPACE__.NS;"
    define(__NAMESPACE__.NS.'__NS__', __NAMESPACE__.NS);
    define(__NS__.'ROOT',             __DIR__.DS.'lib');

    require ROOT.DS.'phuby'.DS.'environment.php';

    spl_autoload_register(__NS__.'Environment::autoload');
    Environment::append_include_path(ROOT);

    set_error_handler(__NS__.'Environment::error_handler');
    Environment::register_error_handler(__NS__.'ErrorHandler::non_static_method_call_error_handler');
    Environment::register_error_handler(__NS__.'ErrorHandler::undefined_constant_error_handler');
}