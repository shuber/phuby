<?php

namespace Phuby {
    require ROOT.DS.'phuby'.DS.'environment.php';

    spl_autoload_register(__NS__.'Environment::autoload');
    Environment::append_include_path(ROOT);

    set_error_handler(__NS__.'Environment::error_handler');
    Environment::append_error_handler(__NS__.'ErrorHandler::non_static_method_call_error_handler');
    Environment::append_error_handler(__NS__.'ErrorHandler::undefined_constant_error_handler');
}